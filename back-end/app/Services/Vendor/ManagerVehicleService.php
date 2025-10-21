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
    protected $managervehicleRepository;
    protected $coachRepository;
    protected $seatRepository;

    public function __construct(ManagerVehicleRepositoryInterface $managervehicleRepository, CoachRepositoryInterface $coachRepository, $seatRepository)
    {
        $this->managervehicleRepository = $managervehicleRepository;
        $this->coachRepository = $coachRepository;
        $this->seatRepository = $seatRepository;
    }


    public function createVehicle(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Bước 1: Tạo Vehicle trước
            $vehicle = $this->managerVehicelRepository->create([
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

                // Chuẩn bị tạo ghế cho coach này
                for ($i = 1; $i <= $coachData['total_seats']; $i++) {
                    $allSeatsToCreate[] = ['coach_id' => $coach->id, 'created_at' => $now, 'updated_at' => $now];
                }

            } elseif ($data['vehicle_type'] === 'train') {
                // --- LOGIC CHO TÀU HỎA ---
                $coachGroups = $data['coaches'];

                // Dùng để đếm số thứ tự cho mỗi loại toa (ví dụ: Toa VIP 1, Toa VIP 2)
                $identifierCounters = [];

                foreach ($coachGroups as $group) {
                    $coachType = $group['coach_type'];
                    $totalSeats = $group['total_seats'];
                    $quantity = $group['quantity'];

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

                        // Chuẩn bị tạo ghế cho coach này
                        for ($j = 1; $j <= $totalSeats; $j++) {
                            $allSeatsToCreate[] = ['coach_id' => $coach->id, 'created_at' => $now, 'updated_at' => $now];
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
        return $this->managervehicleRepository->getByVendor($vendorID);
    }


    public function updateVehicle(Vehicles $vehicle, array $data)
    {
        return $this->managervehicleRepository->update($vehicle, $data);
    }


    public function deleteVehicle(Vehicles $vehicle){
        return $this->managervehicleRepository->delete($vehicle);
    }


}
