<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Response;

class PostcodeControllerTest extends TestCase
{

    /**
     * Test testPostcode200() - response 200 OK
     */
    public function testPostcode200(): void
    {
        $response = $this->get('/postcodes');

        $response->assertStatus(200);
    }

    /**
     * Test testLocations200() - response 200 OK
     */
    public function testLocations200(): void
    {
        $response = $this->get('/locations/AB21 7NF');

        $response->assertStatus(200);
    }

    /**
     * Test testReport200() - response 200 OK
     */
    public function testReport200(): void
    {
        $response = $this->get('/report');

        $response->assertStatus(200);
    }

     /**
     * Test testReportPdf200() - response 200 OK
     */
    public function testReportPdf200(): void
    {
        $response = $this->get('/report/1');

        $response->assertStatus(200);
    }

    /**
     * Test testPostcodeNotFound404() - response 404
     */
    public function testPostcodeNotFound404(): void
    {
        $response = $this->get('/postcodesXXX');

        $response->assertStatus(404);
    }

    /**
     * Test testLocationsBus200() - response 200 OK
     */
    public function testLocationsBus200()
    {
        $response = $this->get('/locations/AB21 7NF/bus')->decodeResponseJson();

        $this->assertEquals(188619, $response[0]['id']);
        $this->assertEquals('Shopping Centre & Academy', $response[0]['name']);
        $this->assertEquals('57.20529668', $response[0]['lat']);
        $this->assertEquals('-2.17651826', $response[0]['lon']);
        $this->assertEquals(56.414257397215756, $response[0]['distance']);

        $this->assertCount(5, $response);
    }

    /**
     * Test testReportJson200() - response 200 OK
     */
    public function testReportJson200()
    {
        $response = $this->get('/report')->decodeResponseJson();

        $this->assertEquals(1569, $response[0]['userId']);
        $this->assertEquals('Dee UCYNW', $response[0]['fullName']);
        $this->assertEquals(2610, $response[0]['houseId']);
        $this->assertEquals('FLAT', $response[0]['propertyType']);
        $this->assertEquals(959318, $response[0]['postcodeID']);
        $this->assertEquals('', $response[0]['district']);
        $this->assertEquals('', $response[0]['locality']);
        $this->assertEquals('Hope Street', $response[0]['street']);
        $this->assertEquals('', $response[0]['site']);
        $this->assertEquals(53, $response[0]['siteNumber']);
        $this->assertEquals('', $response[0]['siteDescription']);
        $this->assertEquals('Flat 2', $response[0]['siteSubdescription']);
        $this->assertEquals(0, $response[0]['likesA']);
        $this->assertEquals('', $response[0]['likeIds']);
        $this->assertEquals('4', $response[0]['likesB']);
        $this->assertEquals(0, $response[0]['matching']);
        $this->assertEquals('', $response[0]['matchingIds']);
        $this->assertEquals(0, $response[0]['differentChats']);
        $this->assertEquals(0, $response[0]['unansweredChats']);
        $this->assertEquals(2, $response[0]['numberOfPeople']);
        $this->assertEquals(0, $response[0]['peopleOlder45']);
    }

    /**
     * Test testPostcodes200() - response 200 OK
     */
    public function testPostcodes200()
    {
        $response = $this->get('/postcodes')->decodeResponseJson();

        $this->assertEquals('AB21 7NF', $response['AB21'][0]);
        $this->assertEquals('AB21 9DG', $response['AB21'][1]);
        $this->assertEquals('AB21 9HY', $response['AB21'][2]);
        $this->assertEquals('AB21 9LL', $response['AB21'][3]);
        $this->assertEquals('AB21 0WB', $response['AB21'][4]);
        $this->assertEquals('AB21 0YG', $response['AB21'][5]);
        $this->assertEquals('AB21 7BD', $response['AB21'][6]);
        $this->assertEquals('AB21 0SR', $response['AB21'][7]);
    }
}