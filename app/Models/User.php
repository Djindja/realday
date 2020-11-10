<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function house()
    {
        return $this->hasOne(House::class);
    }

    public function likesA()
    {
        return $this->hasMany(Like::class, 'a');
    }

    public function likesB()
    {
        return $this->hasMany(Like::class, 'b');
    }

    public function matching()
    {
        $counter = Like::where('a', $this->id)->where('like', 1)->get();

        $i = 0;
        foreach($counter as $c) {
            $match = Like::where('b', $c->a)->where('like', 1)->first();

            if($match != null) {
                $i++;
            }
        }

        return $i;

    }

    public function matchIds()
    {
        $counter = Like::where('a', $this->id)->where('like', 1)->get();

        $ids = [];
        foreach($counter as $c) {
            $match = Like::where('b', $c->a)->where('like', 1)->first();

            if($match != null) {
                $ids[] = $match->id;
            }
        }

        return $ids;

    }


    public function differentChats()
    {
        return Chat::where('from', $this->id)->orWhere('to', $this->id)->count();
    }

    public function unansweredChats()
    {
        $counter = Chat::where('from', $this->id)->get();

        $count = 0;
        foreach($counter as $c) {
            $match = Chat::where('to', $c->id)->first();

            if($match != null) {
                $count++;
            }
        }

        return count($counter) - $count;
    }

    public function people()
    {
        return $this->hasMany(People::class);
    }
}