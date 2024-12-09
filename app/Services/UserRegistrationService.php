<?php

namespace App\Services;

use App\Models\Farm;
use App\Models\User;
use App\Models\Farmer;
use Faker\Provider\ar_EG\Person;
use Illuminate\Support\Facades\DB;
use App\Models\Pds\PersonnelAddress;
use Filament\Notifications\Notification;

class UserRegistrationService
{
    public function registerUser(array $arraydata): User
    {

        $data = reset($arraydata);
        DB::beginTransaction();
        try {

            // dd($data);
            $user = $this->createUser($data);
            $farmer = $this->createFarmer($data, $user);
            $this->createFarm($data, $user);

           // $this->createFarmerApp($data, $farmer);
          //   $this->createPersonnelAddresses($data, $farmer);
            // $this->createPersonnelContactId($data, $farmer);
            // $this->createPersonnelEducBackground($data, $farmer);
            // $this->createPersonnelEligibility($data, $farmer);
            // $this->createPersonnelWorkExp($data, $farmer);
            // $this->createPersonnelVoluntaryWorks($data, $farmer);
            // $this->createPersonnelLnds($data, $farmer);
            // $this->createPersonnelOtherInfo($data, $farmer);
            // $this->createPersonnelStatutory($data, $farmer);
            // $this->createPersonelRefs($data, $farmer);
         //   $roleName = 'panel_user';
          //  $this->assignUserRole($user, $roleName);

            DB::commit();

            return $user;
        } catch (\Throwable $th) {
            DB::rollBack();

            Notification::make()
            ->title('Error')
            ->danger()
            ->body('RegisterUser: ', $th->getMessage())
            ->send();

            // dd('Error: during handleRegistration.', $th->getMessage());
            throw $th;
        }
    }

    protected function createUser(array $data): User
    {


        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    protected function createFarmer(array $data, User $user): Farmer
    {

        return Farmer::create([
           'user_id' => $user->id,
           'app_no' => $data['app_no'] ?? '',
           'crop' => $data['crop'] ?? '',
           'funding_source' => $data['funding_source'] ?? '',
           'date_of_application' => $data['date_of_application'] ?? '',
           'lastname' => $data['lastname'] ?? '',
           'firstname' => $data['firstname'] ?? '',
           'middlename' => $data['middlename'] ?? '',
           'street' => $data['street'] ?? '',
           'barangay' => $data['barangay'] ?? '',
           'municipality' => $data['municipality'] ?? '',
           'province' => $data['province'] ?? '',
           'phone_no' => $data['phone_no'] ?? '',
           'sex' => $data['sex'] ?? '',
           'birthdate' => $data['birthdate'] ?? '',
           'age' => $data['age'] ?? '',
           'civil_status' => $data['civil_status'] ?? '',
           'pwd' => $data['pwd'] ?? '',
           'ip' => $data['ip'] ?? '',
           'bank_name' => $data['bank_name'] ?? '',
           'bank_account_no' => $data['bank_account_no'] ?? '',
           'bank_branch' => $data['bank_branch'] ?? '',
           'bank_name' => $data['bank_name'] ?? '',
           'spouse' => $data['spouse'] ?? '',
           'primary_beneficiaries' => $data['primary_beneficiaries'] ?? '',
           'primary_beneficiaries_age' => $data['primary_beneficiaries_age'] ?? '',
           'primary_beneficiaries_relationship' => $data['primary_beneficiaries_relationship'] ?? '',
           'secondary_beneficiaries' => $data['secondary_beneficiaries'] ?? '',
           'secondary_beneficiaries_age' => $data['secondary_beneficiaries_age'] ?? '',
           'secondary_beneficiaries_relationship' => $data['secondary_beneficiaries_relationship'] ?? '',
            'assignee' => $data['assignee'] ?? '',
           'reason_assignment' => $data['reason_assignment'] ?? '',



        ]);
    }


    protected function createFarm(array $data, User $user): Farm
    {

            dd($data);
        return Farm::create([
           'user_id' => $user->id,
           'lot_hectare' => $data['lot_hectare'] ?? '',
           'sitio' => $data['sitio'] ?? '',
           'barangay' => $data['barangay'] ?? '',
           'municipality' => $data['municipality'] ?? '',
           'province' => $data['province'] ?? '',
           'north' => $data['north'] ?? '',
           'south' => $data['south'] ?? '',
           'street' => $data['street'] ?? '',
           'barangay' => $data['barangay'] ?? '',
           'municipality' => $data['municipality'] ?? '',
           'province' => $data['province'] ?? '',
           'east' => $data['east'] ?? '',
           'west' => $data['west'] ?? '',
           'variety' => $data['variety'] ?? '',
           'planning_method' => $data['planning_method'] ?? '',
           'date_of_sowing' => $data['date_of_sowing'] ?? '',
           'date_of_planning' => $data['date_of_planning'] ?? '',
           'date_of_harvest' => $data['date_of_harvest'] ?? '',
           'population_density' => $data['population_density'] ?? '',
           'age_group' => $data['age_group'] ?? '',
           'no_of_hills' => $data['no_of_hills'] ?? '',
           'land_category' => $data['land_category'] ?? '',
           'soil_type' => $data['soil_type'] ?? '',
           'topography' => $data['topography'] ?? '',
           'source_of_irrigation' => $data['source_of_irrigation'] ?? '',
           'tenurial_status' => $data['tenurial_status'] ?? '',


        ]);
    }



    // protected function createFarmerApp(array $data, Farmer $farmer)
    // {

    //     $farmer->farmerinfo()->create(reset($data[
    //         'farmerinfo']));
    // }


    // protected function createFarmerNames(array $data, Farmer $farmer)
    // {
    //     $farmer->farmernames()->create(reset($data['farmernames']));
    // }

    // protected function createPersonnelEducBackground(array $data, Personnel $farmer)
    // {
    //     $farmer->personnelEducations()->createMany($data['personnelEducations']);
    // }

    // protected function createPersonnelEligibility(array $data, Personnel $farmer)
    // {
    //     $farmer->eligibilities()->createMany($data['eligibilities']);
    // }

    // protected function createPersonnelWorkExp(array $data, Personnel $farmer)
    // {
    //     $farmer->workexperiences()->createMany($data['workexperiences']);
    // }

    // protected function createPersonnelVoluntaryWorks(array $data, Personnel $farmer)
    // {
    //     $farmer->voluntaryworks()->createMany($data['voluntaryworks']);
    // }

    // protected function createPersonnelLnds(array $data, Personnel $farmer)
    // {
    //     $farmer->lnds()->createMany($data['lnds']);
    // }

    protected function createPersonnelOtherInfo(array $data, Farmer $farmer)
    {
        // merge these 3 arrays if not empty
        $otherinfo = array_merge(
            $data['personnelotherinfo'] ?? [],
            $data['recognition'] ?? [],
            $data['membership'] ?? []
        );

        $farmer->personnelotherinfos()->createMany($otherinfo);
    }

    protected function createPersonelRefs(array $data, Farmer $farmer)
    {
        $farmer->references()->createMany($data['references']);
    }

    protected function createPersonnelStatutory(array $data, Farmer $farmer)
    {
        $farmer->statutory()->create([
            'stat_ques34a' => $data['stat_ques34a'] ?? null,
            'stat_ques34_dtl' => $data['stat_ques34_dtl'] ?? '',
            'stat_ques34b' => $data['stat_ques34b'] ?? null,
            'stat_ques35a' => $data['stat_ques35a'] ?? null,
            'stat_ques35a_dtl' => $data['stat_ques35a_dtl'] ?? '',
            'stat_ques35b' => $data['stat_ques35b'] ?? null,
            'stat_ques35b_dtls_date' => $data['stat_ques35b_dtls_date'] ?? '',
            'stat_ques35b_dtls_status' => $data['stat_ques35b_dtls_status'] ?? '',
            'stat_ques36' => $data['stat_ques36'] ?? null,
            'stat_ques36_dtl' => $data['stat_ques36_dtl'] ?? '',
            'stat_ques37' => $data['stat_ques37'] ?? null,
            'stat_ques37_dtl' => $data['stat_ques37_dtl'] ?? '',
            'stat_ques38a_dtl' => $data['stat_ques38a_dtl'] ?? '',
            'stat_ques38b' => $data['stat_ques38b'] ?? null,
            'stat_ques38b_dtl' => $data['stat_ques38b_dtl'] ?? '',
            'stat_ques39' => $data['stat_ques39'] ?? null,
            'stat_ques39_dtl' => $data['stat_ques39_dtl'] ?? '',
            'stat_ques40a' => $data['stat_ques40a'] ?? null,
            'stat_ques40a_dtl' => $data['stat_ques40a_dtl'] ?? '',
            'stat_ques40b' => $data['stat_ques40b'] ?? null,
            'stat_ques40b_dtl' => $data['stat_ques40b_dtl'] ?? '',
            'stat_ques40c' => $data['stat_ques40c'] ?? null,
            'stat_ques40c_dtl' => $data['stat_ques40c_dtl'] ?? '',
            'govt_issued_id' => $data['govt_issued_id'] ?? '',
            'printing_date' => $data['printing_date'] ?? '',
            'govt_issued_id_nbr' => $data['govt_issued_id_nbr'] ?? '',
            'date_place_issuance' => $data['date_place_issuance'] ?? '',
            'department_code' => $data['department_code'] ?? ''
        ]);
    }

    protected function createPersonnelAddresses(array $data, Farmer $farmer)
    {
        $addresses = array_merge($data['addresses_residential'] ?? [], $data['addresses_permanent'] ?? []);
        $farmer->addresses()->createMany($addresses);
    }

    protected function createPersonnelContactId(array $data, Farmer $farmer)
    {
        $farmer->contactId()->create([
            'email_add' => $data['email'],
            'mobile_no' => $data['mobile_no'],
        ]);
    }

    protected function assignUserRole(User $user, $roleName)
    {
        $user->assignRole($roleName);
    }
}
