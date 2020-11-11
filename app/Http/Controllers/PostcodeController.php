<?php

namespace App\Http\Controllers;

use Response;
use App\Models\User;
use App\Models\House;
use App\Models\School;
use App\Models\Busstop;
use App\Models\Address;
use App\Models\Postcode;
use App\Helpers\PropertyType;
use Illuminate\Support\Facades\DB;
use App\Helpers\User as UserHelper;

class PostcodeController extends Controller
{
     /**
     * Get postcodes
     *
     * @return Response
     */
    public function postcodes()
    {
        $postcodes = Postcode::pluck('postcode');

        $array = [];

        foreach($postcodes as $postcode) {
            $arr = explode(' ',trim($postcode));

            if(isset($array[$arr[0]])) {
                array_push($array[$arr[0]], $postcode);
            } else {
                $array[$arr[0]] = [];
                array_push($array[$arr[0]], $postcode);
            }
        }

        return Response::json($array);
    }

    /**
     * Get locations (bus, school, address)
     *
     * @return Response
     */
    public function locations(string $postcode, ?string $filter = null)
    {
        $postcode = Postcode::where('postcode', $postcode)->first();

        if ($filter == 'bus') {
            return Response::json(Busstop::findClosest($postcode->latitude, $postcode->longitude));

        } elseif ($filter == 'school') {
            return Response::json(School::where('postcode_id', $postcode->id)->get());

        } elseif ($filter == 'address') {
            return Response::json(Address::where('postcode_id', $postcode->id)->get());
        }

        if ($filter == null) {
            return Response::json([
                'bus' => Busstop::findClosest($postcode->latitude, $postcode->longitude),
                'school' => School::where('postcode_id', $postcode->id)->get(),
                'address' => Address::where('postcode_id', $postcode->id)->get(),
            ]);
        }

    }

    /**
     * Get reports
     *
     * @return Response
     */
    public function reports(?string $pdf = null)
    {
        $users = User::limit(50)->get();
        $o = [];

        $resp = [];

        foreach($users as $user) {
            $o["userId"] = $user->id;
            $o["fullName"] = $user->name . " " . $user->surname;
            $o["houseId"] = $user->house->id;
            $o["propertyType"] = PropertyType::propertyTypeConvert($user->house->propertytype);
            $o["postcodeID"] = $user->house->address->postcode_id;
            $o["district"] = $user->house->address->district;
            $o["locality"] = $user->house->address->locality;
            $o["street"] = $user->house->address->street;
            $o["site"] = $user->house->address->site;
            $o["siteNumber"] = $user->house->address->site_number;
            $o["siteDescription"] = $user->house->address->site_description;
            $o["siteSubdescription"] = $user->house->address->site_subdescription;

            $o["likesA"] = $user->likesA->where('like', 1)->count();
            $o["likeIds"] = UserHelper::returnListIds($user->likesA->where('like', 1));
            $o["likesB"] = $user->likesB->where('like', 1)->count();

            $o["matching"] = $user->matching();
            $o["matchingIds"] = implode(", ", $user->matchIds());
            $o["differentChats"] = $user->differentChats();
            $o["unansweredChats"] = $user->unansweredChats();
            $o["numberOfPeople"] = $user->people->count();
            $o["peopleOlder45"] = $user->people->where('sex', 'M')->where('age', '>', 45)->count();
            $resp[] = $o;
        }

        if ($pdf == 1) {
            echo "<table><tr><th>User ID</th><th>Full Name</th><th>House ID</th><th>Property Type</th><th>Postcode ID</th><th>District</th><th>Locality</th>
            <th>Street</th><th>Site</th><th>Site Number</th><th>Site Description</th><th>Site Subdescription</th><th>Likes A</th><th>Like IDs</th>
            <th>Likes B</th><th>Matching</th><th>Matching Ids</th><th>Different Chats</th><th>Unanswered Chats</th>
            <th>Number od People</th><th>People older than 45</th></tr>";

            foreach($resp as $item){
                echo "<tr><td>".$item['userId']."</td><td>".$item['fullName']."</td><td>".$item['houseId']."</td><td>".$item['propertyType']."</td>
                <td>".$item['postcodeID']."</td><td>".$item['district']."</td><td>".$item['locality']."</td><td>".$item['street']."</td><td>".$item['site']."</td>
                <td>".$item['siteNumber']."</td><td>".$item['siteDescription']."</td><td>".$item['siteSubdescription']."</td><td>".$item['likesA']."</td>
                <td>".$item['likeIds']."</td><td>".$item['likesB']."</td><td>".$item['matching']."</td><td>".$item['matchingIds']."</td>
                <td>".$item['differentChats']."</td><td>".$item['unansweredChats']."</td><td>".$item['numberOfPeople']."</td><td>".$item['peopleOlder45']."</td></tr>";
            }

            echo "</table>";
        }


        return Response::json($resp);

    }
}