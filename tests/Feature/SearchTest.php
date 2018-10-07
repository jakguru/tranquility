<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Helpers\SearchHelper;

class SearchTest extends TestCase
{
    /**
     * Try out verious search methods against the User Model
     */
    public function testPartialNameSearch()
    {
        $fname_search = SearchHelper::search('Super', ['App\\User']);
        $this->assertContains(true, [
            is_a($fname_search, '\Illuminate\Database\Eloquent\Collection'),
            is_array($fname_search)
        ]);
        if (is_a($fname_search, '\Illuminate\Database\Eloquent\Collection')) {
            $this->assertEquals(1, $fname_search->count());
            $first = $fname_search->first();
            $this->assertInstanceOf('App\\User', $first);
            $this->assertEquals(1, $first->id);
        }
        $lname_search = SearchHelper::search('User', ['App\\User']);
        $this->assertContains(true, [
            is_a($lname_search, '\Illuminate\Database\Eloquent\Collection'),
            is_array($lname_search)
        ]);
        if (is_a($lname_search, '\Illuminate\Database\Eloquent\Collection')) {
            $this->assertEquals(1, $lname_search->count());
            $first = $lname_search->first();
            $this->assertInstanceOf('App\\User', $first);
            $this->assertEquals(1, $first->id);
        }
    }

    public function testFullNameSearch()
    {
        $name_search = SearchHelper::search('Super User', ['App\\User']);
        $this->assertContains(true, [
            is_a($name_search, '\Illuminate\Database\Eloquent\Collection'),
            is_array($name_search)
        ]);
        if (is_a($name_search, '\Illuminate\Database\Eloquent\Collection')) {
            $this->assertEquals(1, $name_search->count());
            $first = $name_search->first();
            $this->assertInstanceOf('App\\User', $first);
            $this->assertEquals(1, $first->id);
        }
    }

    public function testEmailSearch()
    {
        $email_search = SearchHelper::search('sudo@localhost.local', ['App\\User']);
        $this->assertContains(true, [
            is_a($email_search, '\Illuminate\Database\Eloquent\Collection'),
            is_array($email_search)
        ]);
        if (is_a($email_search, '\Illuminate\Database\Eloquent\Collection')) {
            $this->assertEquals(1, $email_search->count());
            $first = $email_search->first();
            $this->assertInstanceOf('App\\User', $first);
            $this->assertEquals(1, $first->id);
        }
    }
}
