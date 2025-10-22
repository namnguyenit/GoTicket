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
            // Bước 1: Tạo Vehicle trước
            $vehicle = $this->managerVehicleRepository->create([
                'vendor_id' => auth()->user()->vendor->id,
                'name' => $data['name'],
                'vehicle_type' => $data['vehicle_type'],
                'license_plate' => $data['license_plate'] ?? null,
            ]);

            $allSeatsToCreate = [];
            $now = Carbon::now();

            // Bước 2: Xử lý logic dựa trên loại phương tiện
            if ($data['vehicle_type'] === 'bus') {
                // --- LOGIC CHO XE BUS ---
                $coachData = $data['coach'];
                $coach = $this->coachRepository->create([
                    'vehicle_id' => $vehicle->id,
                    'identifier' => 'Main Coach', // Xe bus chỉ có 1 coach chính
                    'coach_type' => $coachData['coach_type'],
                    'total_seats' => $coachData['total_seats'],
                ]);

                // Chuẩn bị tạo ghế cho coach này (bảng seats KHÔNG có timestamps)
                for ($i = 1; $i <= $coachData['total_seats']; $i++) {
                    $seatNumber = 'S' . str_pad((string)$i, 2, '0', STR_PAD_LEFT);
                    $allSeatsToCreate[] = [
                        'coach_id'    => $coach->id,
                        'seat_number' => $seatNumber,
                    ];
                }

            } elseif ($data['vehicle_type'] === 'train') {
                // --- LOGIC CHO TÀU HỎA ---
                $coachGroups = $data['coaches'];

                // Dùng để đếm số thứ tự cho mỗi loại toa (ví dụ: Toa VIP 1, Toa VIP 2)
                $identifierCounters = [];

                foreach ($coachGroups as $group) {
                    $coachType = $group['coach_type'];
                    $quantity = $group['quantity'];

                    // Quy tắc tính số ghế:
                    // - seat_soft: 40 ghế/toa
                    // - seat_VIP: 6 khoang x 4 ghế = 24 ghế/toa
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

                        // seat_number: đánh số theo khoang đối với VIP, theo thứ tự đối với seat_soft
                        if ($coachType === 'seat_VIP') {
                            // 6 khoang, mỗi khoang 4 ghế: K1A..K1D, K2A..K2D ...
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

            // Bước 3: Tạo tất cả các ghế cần thiết trong một lệnh duy nhất
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
