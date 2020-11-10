<?php

namespace App\Http\Controllers;

use Response;
use App\Models\Postcode;
use App\Models\Busstop;
use App\Models\Address;
use App\Models\House;
use App\Models\School;
use App\Models\User;
use App\Helpers\User as UserHelper;
use Illuminate\Support\Facades\DB;

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
     * Get locations
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
        // $users = DB::table('users')
        //             ->leftJoin('houses', 'users.id', '=', 'houses.user_id')
        //             ->leftJoin('addresses', 'houses.postcode_id', '=', 'addresses.postcode_id')
        //             // ->leftJoin('likes', function($join) {
        //             //     $join->on('users.id', '=', 'likes.a')
        //             //     ->where('likes.like', '>', 0);
        //             // })
        //             ->leftJoin('people', 'users.id', '=', 'people.user_id')
        //             ->leftJoin('chats', 'users.id', '=', 'chats.from')
        //             ->select('users.id as userId', DB::raw("CONCAT(users.name,' ',users.surname) as fullName"), 'houses.id as houseId',
        //             'houses.propertytype as propertyType', 'addresses.postcode_id as postcode', 'addresses.district', 'addresses.locality', 'addresses.street', 'addresses.site', 'addresses.site_number as siteNumber',
        //             'addresses.site_description as desc', 'addresses.site_subdescription as sub',
        //             // DB::raw('likes.like as likes'),
        //             DB::raw('COUNT(people.user_id) as numOfPeople'),
        //             DB::raw('COUNT(chats.from) as numOfChats'))
        //             ->having('people.sex', 'M')
        //             ->andHaving('people.age', '>', 45)
        //             ->limit(1)
        //             ->groupBy('userId','houseId', 'addresses.district', 'addresses.locality', 'addresses.street', 'addresses.site', 'siteNumber', 'desc', 'sub')
        //             ->get();

        $users = User::limit(50)->get();
        $o = [
            "userId" => ''
        ];

        $resp = [];

        foreach($users as $user) {
            $o["userId"] = $user->id;
            $o["fullName"] = $user->name . " " . $user->surname;
            $o["houseId"] = $user->house->id;
            $o["property_type"] = $user->house->propertytype;
            $o["likesA"] = $user->likesA->where('like', 1)->count();
            $o["likesB"] = $user->likesB->where('like', 1)->count();
            $o["listIds"] = UserHelper::returnListIds($user->likesA->where('like', 1));
            $o["matching"] = $user->matching();
            $o["matchingIds"] = implode(", ", $user->matchIds());
            $o["differentChats"] = $user->differentChats();
            $o["unansweredChats"] = $user->unansweredChats();
            $o["people"] = $user->people->count();
            $o["peopleOlder45"] = $user->people->where('sex', 'M')->where('age', '>', 45)->count();
            $o["postcodeID"] = $user->house->address->postcode_id;
            $o["district"] = $user->house->address->district;
            $o["locality"] = $user->house->address->locality;
            $o["street"] = $user->house->address->street;
            $o["site"] = $user->house->address->site;

            $resp[] = $o;
        }

        return Response::json($resp);

    }

}