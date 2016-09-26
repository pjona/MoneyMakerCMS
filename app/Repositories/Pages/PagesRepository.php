<?php

namespace App\Repositories\Pages;

use Illuminate\Support\Facades\View;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Contracts\Container\Container;
use App\Repositories\Pages\Traits\ParsePageTrait;
use Rinvex\Repository\Repositories\EloquentRepository;

class PagesRepository extends EloquentRepository
{
    use ParsePageTrait;

    private $blade;

    public function __construct(Container $container)
    {
        $this->setContainer($container)
             ->setModel(\App\Models\Pages\Page::class)
             ->setRepositoryId(md5('monkeymaker.repository.pages'));

        $this->blade = $container['blade.compiler'];
    }

    public function get()
    {
        return Datatables::of($this->createModel()->query())
             ->editColumn('uri', function ($page) {
                return "";
                 // return "<label class='label label-warning'>" . route($page->route) ."</label>";
             })->editColumn('active', function ($page) {
                 if ($page->active) {
                     return "<label class='label label-success'>Active</label>";
                 }

                 return "<label class='label label-warning'>Disabled</label>";
             })
             ->editColumn('type', function ($page) {
                 if ($page->type === 'database') {
                     return "<label class='label label-success'>".$page->type.'</label>';
                 }
                 return "<label class='label label-warning'>".$page->type.'</label>';
             })
             ->addColumn('actions', function ($page) {
                 return $page->action_buttons;
             })
            ->make(true);
    }

    public function store($id, array $input)
    {
        $data = array_except($input, ['page_id']);

        return !$id ? $this->create($data) : $this->update($id, $data);
    }

    public function render($uri)
    {
        if ($page = $this->where('uri', '=', $uri)->where('active', '=', 1)->findAll()->first()) {
            if ($page->type === 'database') {
                $content = $this->parse($page->content);
            }
            
            return collect(['content' => $content, 'page' => $page ]);
        }

        return false;
    }

    public function findActive()
    {
        return $this->findWhere(['active', '=', 1])
            ->filter(function ($page, $key) {
                return $page->ab == false;
            });
    }
}