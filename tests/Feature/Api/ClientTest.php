<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();

        $this->withoutExceptionHandling();

        $this->seed('RolesTableSeeder');
        $this->seed('LanguagesTableSeeder');
    }

    /** @test */
    public function an_individual_can_submit_an_interpreter_quote() {
        $this->post('/api/clients', [
            'department' => 'some department',
            "address_line_1" => "asddsa",
            "address_line_2" => "sdasd",
            "appointment_date" => "2019-02-20",
            "client_address_line_1" => "test",
            "client_address_line_2" => "test",
            "client_county" => "test",
            "client_postcode" => "test",
            "client_reference" => "dfsffdsdf",
            "contact_information" => "test",
            "contact_method" => [1, 2],
            "contact_number" => "123457",
            "county" => "sfdsfdsfd",
            "email" => "test@test3.com",
            "first_name" => "fgfg",
            "from_language_id" => 1,
            "gender" => 0,
            "is_organisation" => 0,
            "job_type" => "interpreter",
            "last_name" => "gffgg",
            "password" => "password",
            "personal_identity_number" => "test",
            "postcode" => "cv45 3",
            "require_qualified" => 0,
            "service_type" => 0,
            "special_requirements" => "test",
            "start_time" => "01:15",
            "duration" => "1hr 15minss",
            "title" => 0,
            "to_language_id" => 2,
            "user_first_name" => "test",
            "user_last_name" => "test",
            "user_title" => 1,
            'file_reference' => 'some reference',
            "date_of_birth" => "2019-02-20",
        ])
        ->assertOk();
    }

   /** @test */
    public function an_organisation_can_submit_an_interpreter_quote() {
        $this->post('/api/clients', [
            'department' => 'some department',
            "address_line_1" => "test",
            "address_line_2" => "test",
            "appointment_date" => "2019-02-14",
            "client_address_line_1" => "test",
            "client_address_line_2" => "test",
            "client_county" => "test",
            "client_postcode" => "test",
            "client_reference" => "fdfd",
            "company_number" => "test",
            "contact_information" => "test",
            "contact_method" => 1,
            "contact_number" => "123457",
            "county" => "test",
            "email" => "test@test4.com",
            "first_name" => "fgfg",
            "from_language_id" => 1,
            "gender" => 1,
            "is_organisation" => 1,
            "job_type" => "interpreter",
            "last_name" => "gffgg",
            "organisation_address_line_1" => "test",
            "organisation_address_line_2" => "test",
            "organisation_company" => "test",
            "organisation_county" => "test",
            "organisation_postcode" => "test",
            "organisation_email" => "test@test.com",
            "password" => "testtest",
            "personal_identity_number" => "test",
            "postcode" => "test",
            "require_qualified" => 0,
            "service_type" => 0,
            "special_requirements" => "test",
            "start_time" => "01:15",
            "duration" => "15mins",
            "title" => 0,
            "to_language_id" => 3,
            "user_first_name" => "test",
            "user_last_name" => "test",
            "user_title" => 0,
            "vat_number" => "test",
            'file_reference' => 'some reference',
            "date_of_birth" => "2019-02-20",
        ])
        ->assertOk();
    }

    /** @test */
    public function an_individual_can_submit_an_translator_quote() {
        $this->post('/api/clients', [
            "affidavit" => 0,
            "affirmation" => 1,
            "client_address_line_1" => "test",
            "client_address_line_2" => "test",
            "client_county" => "test",
            "client_postcode" => "test",
            "client_reference" => "test",
            "contact_method" => 1,
            "contact_number" => "123457",
            "email" => "test@test88.com",
            "file" => [
                "path" => ["some-path"],
                "name" => ["some-name"]
            ],
            "first_name" => "fgfg",
            "from_language_id" => 1,
            "is_organisation" => 0,
            "job_type" => "translator",
            "last_name" => "gffgg",
            "password" => "password",
            "special_requirements" => "test",
            "target_date" => "2019-02-21",
            "title" => 0,
            "to_language_id" => 2,
            "word_count" => 5983,
            'notes' => 'some notes',
            "service_type" => 0,
        ])
        ->assertOk();

    }

    /** @test */
    public function an_organisation_can_submit_an_translator_quote() {
        $this->post('/api/clients', [
            "affidavit" => 0,
            "affirmation" => 1,
            "client_address_line_1" => "test",
            "client_address_line_2" => "test",
            "client_county" => "test",
            "client_postcode" => "test",
            "client_reference" => "ererwer",
            "company_number" => "test",
            "contact_method" => 1,
            "contact_number" => "123457",
            "email" => "test@test4443.com",
            "file" => [
                "path" => ["some-path"],
                "name" => ["some-name"]
            ],
            "first_name" => "test",
            "from_language_id" => 1,
            "is_organisation" => 1,
            "job_type" => "translator",
            "last_name" => "test",
            "organisation_address_line_1" => "test",
            "organisation_address_line_2" => "test",
            "organisation_company" => "test",
            "organisation_county" => "test",
            "organisation_postcode" => "test",
            "organisation_email" => "test@test.com",
            "password" => "testtest",
            "special_requirements" => "test",
            "target_date" => "2019-02-21",
            "title" => 0,
            "to_language_id" => 2,
            "vat_number" => "test",
            "word_count" => 33,
            'notes' => 'some notes',
            "service_type" => 0,
        ])
        ->assertOk();
    }
}
