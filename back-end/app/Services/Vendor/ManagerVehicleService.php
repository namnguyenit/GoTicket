<?php

namespace App\Services\Vendor;

use App\Models\Vehicles;
use App\Repositories\Vendor\ManagerVehicleRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Vendor\CoachRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Repositories\Vendor\SeatRepositoryInterface;



class ManagerVehicleService{
    protected $managerVehicleRepository;
    protected $coachRepository;
    protected $seatRepository;

    public function __construct(
        ManagerVehicleRepositoryInterface $managerVehicleRepository,
        CoachRepositoryInterface $coachRepository,
        SeatRepositoryInterface $seatRepository
    )
    {
        $this->managerVehicleRepository = $managerVehicleRepository;
        $this->coachRepository = $coachRepository;
        $this->seatRepository = $seatRepository;
    }


    public function createVehicle(array $data)
    {
        return DB::transaction(function () use ($data) {

            $vehicle = $this->managerVehicleRepository->create([
                'vendor_id' => auth()->user()->vendor->id,
                'name' => $data['name'],
                'vehicle_type' => $data['vehicle_type'],
                'license_plate' => $data['license_plate'] ?? null,
            ]);

            $allSeatsToCreate = [];
            $now = Carbon::now();

            if ($data['vehicle_type'] === 'bus') {

                $coachData = $data['coach'];
                $coach = $this->coachRepository->create([
                    'vehicle_id' => $vehicle->id,
                    'identifier' => 'Main Coach', // Xe bus chỉ có 1 coach chính
                    'coach_type' => $coachData['coach_type'],
                    'total_seats' => $coachData['total_seats'],
                ]);

                for ($i = 1; $i <= $coachData['total_seats']; $i++) {
                    $seatNumber = 'S' . str_pad((string)$i, 2, '0', STR_PAD_LEFT);
                    $allSeatsToCreate[] = [
                        'coach_id'    => $coach->id,
                        'seat_number' => $seatNumber,
                    ];
                }

            } elseif ($data['vehicle_type'] === 'train') {

                $coachGroups = $data['coaches'];

                $identifierCounters = [];

                foreach ($coachGroups as $group) {
                    $coachType = $group['coach_type'];
                    $quantity = $group['quantity'];



                    $totalSeats = $coachType === 'seat_VIP' ? 6 * 4 : 40;

                    if (!isset($identifierCounters[$coachType])) {
                        $identifierCounters[$coachType] = 1;
                    }

                    for ($i = 0; $i < $quantity; $i++) {
                        $identifier = $coachType . ' ' . $identifierCounters[$coachType]++;

                        $coach = $this->coachRepository->create([
                            'vehicle_id' => $vehicle->id,
                            'identifier' => $identifier,
                            'coach_type' => $coachType,
                            'total_seats' => $totalSeats,
                        ]);

                        if ($coachType === 'seat_VIP') {

                            for ($k = 1; $k <= 6; $k++) {
                                foreach (['A','B','C','D'] as $pos) {
                                    $allSeatsToCreate[] = [
                                        'coach_id'    => $coach->id,
                                        'seat_number' => 'K' . $k . $pos,
                                    ];
                                }
                            }
                        } else {
                            for ($j = 1; $j <= $totalSeats; $j++) {
                                $seatNumber = 'S' . str_pad((string)$j, 2, '0', STR_PAD_LEFT);
                                $allSeatsToCreate[] = [
                                    'coach_id'    => $coach->id,
                                    'seat_number' => $seatNumber,
                                ];
                            }
                        }
                    }
                }
            }

            if (!empty($allSeatsToCreate)) {
                $this->seatRepository->createMany($allSeatsToCreate);
            }

            return $vehicle;
        });
    }

    public function getVehicleByVendor()
    {
        $vendorID = auth()->user()->vendor->id;
        return $this->managerVehicleRepository->getByVendor($vendorID);
    }


    public function updateVehicle(Vehicles $vehicle, array $data)
    {
        return $this->managerVehicleRepository->update($vehicle, $data);
    }


    public function deleteVehicle(Vehicles $vehicle){
        return $this->managerVehicleRepository->delete($vehicle);
    }

    public function addCoaches(Vehicles $vehicle, array $coaches){
        return DB::transaction(function() use ($vehicle, $coaches){
            $created = [];
            foreach($coaches as $group){
                $coachType = $group['coach_type'];
                $quantity = (int) ($group['quantity'] ?? 1);
                $totalSeats = $coachType === 'seat_VIP' ? 24 : 40;
                for($i=0;$i<$quantity;$i++){
                    $identifier = strtoupper($coachType).' '.uniqid();
                    $coach = $this->coachRepository->create([
                        'vehicle_id' => $vehicle->id,
                        'identifier' => $identifier,
                        'coach_type' => $coachType,
                        'total_seats' => $totalSeats,
                    ]);
                    $created[] = $coach;
                    if($coachType === 'seat_VIP'){
                        $batch = [];
                        for($k=1;$k<=6;$k++){
                            foreach(['A','B','C','D'] as $pos){
                                $batch[] = [ 'coach_id'=>$coach->id, 'seat_number'=>'K'.$k.$pos ];
                            }
                        }
                        $this->seatRepository->createMany($batch);
                    } else {
                        $batch = [];
                        for($s=1;$s<=40;$s++){
                            $batch[] = ['coach_id'=>$coach->id,'seat_number'=>'S'.str_pad((string)$s,2,'0',STR_PAD_LEFT)];
                        }
                        $this->seatRepository->createMany($batch);
                    }
                }
            }
            return $created;
        });
    }

    public function removeCoach(\App\Models\Coaches $coach){
        return $this->coachRepository->delete($coach);
    }

}
