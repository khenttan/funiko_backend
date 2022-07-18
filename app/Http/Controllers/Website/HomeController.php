<?php

namespace App\Http\Controllers\Website;

use App\Models\News;

use App\Models\Page;
use App\Models\Content;
use App\Models\ProductCategory;

class HomeController extends WebsiteController
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $this->showPageBanner = true;

        $news = News::whereHas('photos')->with('photos')->isActiveDates()->orderBy('active_from', 'DESC')->get();

        $section1 =   Content::where('id',5)->first();
        $section2 =   Content::where('id',6)->first();
        $section3 =   Content::where('id',7)->first();
        $section4 =   Content::where('id',8)->first();
        // dd($section1);
        return $this->view('home',compact('section1','section2','section3','section4'))
            ->with('news', $news);
    }
}
