<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use App\Models\CompanyJob;
use App\Models\Skill;
use App\Models\Application;
use App\Models\ApplicationStatus;
use App\Models\JobStatus;
use App\Models\Applicant;
use App\Models\JobSetup;
use App\Models\JobType;
use Illuminate\Queue\Middleware\Skip;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles/permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | PERMISSIONS
        |--------------------------------------------------------------------------
        */
        $permissions = [
            'create job',
            'edit job',
            'delete job',
            'view applicants',
            'apply job',
        ];

        foreach ($permissions as $perm) {
            Permission::create(['name' => $perm]);
        }

        /*
        |--------------------------------------------------------------------------
        | ROLES
        |--------------------------------------------------------------------------
        */
        $admin = Role::create(['name' => 'admin']);
        $recruiter = Role::create(['name' => 'recruiter']);
        $applicantRole = Role::create(['name' => 'applicant']);

        // Assign permissions
        $admin->givePermissionTo(Permission::all());

        $recruiter->givePermissionTo([
            'create job',
            'edit job',
            'delete job',
            'view applicants',
        ]);

        $applicantRole->givePermissionTo([
            'apply job'
        ]);

        /*
        |--------------------------------------------------------------------------
        | USERS
        |--------------------------------------------------------------------------
        */
        $adminUser = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'phone_no' => '09' . rand(100000000, 999999999),
            'address' => 'Metro Manila',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $adminUser->assignRole('admin');

        $recruiterUser = User::create([
            'first_name' => 'Recruiter',
            'last_name' => 'User',
            'phone_no' => '09' . rand(100000000, 999999999),
            'address' => 'Metro Manila',
            'email' => 'recruiter@test.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $recruiterUser->assignRole('recruiter');

        $applicantUser = User::create([
            'first_name' => 'Applicant',
            'last_name' => 'User',
            'phone_no' => '09' . rand(100000000, 999999999),
            'address' => 'Metro Manila',
            'email' => 'applicant@test.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $applicantUser->assignRole('applicant');

        /*
        |--------------------------------------------------------------------------
        | APPLICANT PROFILE
        |--------------------------------------------------------------------------
        */
        $applicant = Applicant::create([
            'user_id' => $applicantUser->id,
            'headline' => 'Junior Developer',
            'bio' => 'Passionate developer',
            'expected_salary_min' => 20000,
            'expected_salary_max' => 40000,
        ]);

        /*
        |--------------------------------------------------------------------------
        | JOB TYPES
        |--------------------------------------------------------------------------
        */
        $fullTime = JobType::create(['name' => 'Full-time']);

        $otherJobTypes = ['Part-time', 'Contract', 'Internship'];

        foreach ($otherJobTypes as $jobType) {
            JobType::create(['name' => $jobType]);
        }

        /*
        |--------------------------------------------------------------------------
        | JOB SETUP
        |--------------------------------------------------------------------------
        */
        $onsite = JobSetup::create(['name' => 'On-site']);
        $remote = JobSetup::create(['name' => 'Remote']);
        $hybrid = JobSetup::create(['name' => 'Hybrid']);

        /*
        |--------------------------------------------------------------------------
        | STATUSES
        |--------------------------------------------------------------------------
        */
        $openStatus = JobStatus::create(['name' => 'Open']);
        $closedStatus = JobStatus::create(['name' => 'Closed']);

        $otherJobStatuses = ['Draft', 'Filled', 'Paused'];

        foreach ($otherJobStatuses as $jobStatus) {
            JobStatus::create(['name' => $jobStatus]);
        }

        $appliedStatus = ApplicationStatus::create(['name' => 'Applied']);
        $interviewStatus = ApplicationStatus::create(['name' => 'Interview']);

        $otherApplicationStatuses = ['Under Review', 'Shortlisted', 'Job Offer', 'Accepted', 'Rejected'];

        foreach ($otherApplicationStatuses as $applicationStatus) {
            ApplicationStatus::create(['name' => $applicationStatus]);
        }

        /*
        |--------------------------------------------------------------------------
        | COMPANY
        |--------------------------------------------------------------------------
        */
        $company = Company::create([
            'created_by' => $recruiterUser->id,
            'name' => 'Tech Corp',
            'description' => 'IT Company',
            'address' => 'Manila',
        ]);

        /*
        |--------------------------------------------------------------------------
        | JOBS
        |--------------------------------------------------------------------------
        */
        $job = CompanyJob::create([
            'created_by' => $recruiterUser->id,
            'company_id' => $company->id,
            'job_status_id' => $openStatus->id,
            'job_type_id' => $fullTime->id,
            'job_setup_id' => $onsite->id,
            'title' => 'Laravel Developer',
            'description' => 'Build APIs using Laravel',
            'location' => 'Remote',
            'salary_min' => 30000,
            'salary_max' => 60000,
        ]);

        /*
        |--------------------------------------------------------------------------
        | SKILLS
        |--------------------------------------------------------------------------
        */
        $php = Skill::create(['name' => 'PHP', 'created_by_user' => false]);
        $laravel = Skill::create(['name' => 'Laravel', 'created_by_user' => false]);
        $vue = Skill::create(['name' => 'Vue.js', 'created_by_user' => false]);

        $otherSkills = ['Typescript', 'Javascript', 'C++', 'C#', 'Java', 'SQL', 'MySql'];

        foreach ($otherSkills as $skill) {
            Skill::create(['name' => $skill, 'created_by_user' => false]);
        }

        // Attach skills
        $job->skills()->attach([$php->id, $laravel->id]);
        $applicantUser->skills()->attach([$php->id, $vue->id]);

        /*
        |--------------------------------------------------------------------------
        | APPLICATION
        |--------------------------------------------------------------------------
        */
        Application::create([
            'user_id' => $applicantUser->id,
            'job_id' => $job->id,
            'applicant_status_id' => $appliedStatus->id,
            'cover_letter' => 'I am very interested in this job.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | MORE RECRUITERS
        |--------------------------------------------------------------------------
        */
        $recruiters = [];

        for ($i = 1; $i <= 3; $i++) {
            $user = User::create([
                'first_name' => "Recruiter$i",
                'last_name' => 'User',
                'email' => "recruiter$i@test.com",
                'phone_no' => '09' . rand(100000000, 999999999),
                'address' => 'Metro Manila',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);

            $user->assignRole('recruiter');

            $recruiters[] = $user;
        }

        /*
        |--------------------------------------------------------------------------
        | MORE APPLICANTS
        |--------------------------------------------------------------------------
        */
        $applicants = [];

        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'first_name' => "Applicant$i",
                'last_name' => 'User',
                'phone_no' => '09' . rand(100000000, 999999999),
                'address' => 'Metro Manila',
                'email' => "applicant$i@test.com",
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);

            $user->assignRole('applicant');

            $profile = Applicant::create([
                'user_id' => $user->id,
                'headline' => 'Software Developer',
                'bio' => 'Eager to learn and grow',
                'expected_salary_min' => rand(20000, 30000),
                'expected_salary_max' => rand(40000, 60000),
            ]);

            $applicants[] = $user;
        }

        /*
        |--------------------------------------------------------------------------
        | MORE COMPANIES + JOBS
        |--------------------------------------------------------------------------
        */
        $allRecruiters = array_merge([$recruiterUser], $recruiters);

        $jobs = [];

        foreach ($allRecruiters as $index => $recruiter) {

            $company = Company::create([
                'created_by' => $recruiter->id,
                'name' => 'Company ' . ($index + 1),
                'description' => 'Sample company',
                'address' => 'Manila',
            ]);

            for ($j = 1; $j <= 2; $j++) {
                $job = CompanyJob::create([
                    'created_by' => $recruiter->id,
                    'company_id' => $company->id,
                    'job_status_id' => $openStatus->id,
                    'job_type_id' => $fullTime->id,
                    'job_setup_id' => $onsite->id,
                    'title' => "Developer Position $j",
                    'description' => 'Job description here',
                    'location' => 'Remote',
                    'salary_min' => rand(25000, 40000),
                    'salary_max' => rand(50000, 80000),
                ]);

                $jobs[] = $job;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | ASSIGN SKILLS RANDOMLY
        |--------------------------------------------------------------------------
        */
        $allSkills = Skill::all();

        foreach ($applicants as $user) {
            $user->skills()->attach(
                $allSkills->random(rand(1, 5))->pluck('id')->toArray()
            );
        }

        foreach ($jobs as $job) {
            $job->skills()->attach(
                $allSkills->random(rand(1, 5))->pluck('id')->toArray()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | APPLICATIONS (RANDOM)
        |--------------------------------------------------------------------------
        */
        foreach ($applicants as $user) {
            foreach ($jobs as $job) {
                if (rand(0, 1)) {
                    Application::create([
                        'user_id' => $user->id,
                        'job_id' => $job->id,
                        'applicant_status_id' => $appliedStatus->id,
                        'cover_letter' => 'I am interested in this job.',
                    ]);
                }
            }
        }
    }
}
