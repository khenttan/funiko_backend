<?php

namespace App\Http\Controllers\Admin\Pages;

use App\Models\Banner;
use App\Models\PageContent;
use App\Models\Page;
use App\Http\Controllers\Admin\AdminController;

class PagesController extends AdminController
{
    /**
     * Display a listing of page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        save_resource_url();

        $items = Page::with('parent')->get();

        return $this->view('pages.index')->with('items', $items);
    }

    /**
     * Show the form for creating a new page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $parents = Page::getAllList();
        $banners = Banner::getAllList();

        return $this->view('pages.create_edit', compact('parents', 'banners'));
    }

    /**
     * Store a newly created page in storage.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store()
    {
        $attributes = request()->validate(Page::$rules, Page::$messages);

        $attributes['is_header'] = (bool) input('is_header');
        $attributes['is_hidden'] = (bool) input('is_hidden');
        $attributes['is_footer'] = (bool) input('is_footer');
        $attributes['is_featured'] = (bool) input('is_featured');
        $attributes['url_parent_id'] = (int) $attributes['url_parent_id'] === 0 ? $attributes['parent_id'] : $attributes['url_parent_id'];

        $page = $this->createEntry(Page::class, $attributes);

        if ($page) {
            $page->updateUrl()->save();
            $page->banners()->sync(input('banners'));
        }

        return redirect_to_resource();
    }

    /**
     * Display the specified page.
     *
     * @param Page $page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Page $page)
    {
        return $this->view('pages.show')->with('item', $page);
    }

    /**
     * Show the form for editing the specified page.
     *
     * @param Page $page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Page $page)
    {
        // fix for when you
        // edit sections from the edit page
        // or delete photo/document then
        // need to update resource url
        save_resource_url();

        $parents = Page::getAllList();
        $banners = Banner::getAllList();

        return $this->view('pages.create_edit', compact('parents', 'banners'))
            ->with('item', $page);
    }

    /**
     * Update the specified page in storage.
     *
     * @param Page $page
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Page $page)
    {
        $attributes = request()->validate(Page::$rules, Page::$messages);

        $attributes['is_header'] = (bool) input('is_header');
        $attributes['is_hidden'] = (bool) input('is_hidden');
        $attributes['is_footer'] = (bool) input('is_footer');
        $attributes['is_featured'] = (bool) input('is_featured');
        $page = $this->updateEntry($page, $attributes);
        $page->updateUrl()->save();
        $page->banners()->sync(input('banners'));

        return redirect_to_resource();
    }

    /**
     * Remove the specified page from storage.
     *
     * @param Page $page
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Page $page)
    {
        $this->deleteEntry($page, request());

        return redirect_to_resource();
    }
}
