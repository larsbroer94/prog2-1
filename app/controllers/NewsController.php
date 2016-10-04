<?php

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;

class NewsController extends ControllerBase
{

//    Show the index of news
    public function IndexAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'News', $_POST);
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $news = News::find();

        if (count($news) == 0) {
            $this->flash->notice("There is currently no news");

            $this->dispatcher->forward([
                "controller" => "news",
                "action" => "index"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $news,
            'limit'=> 10,
            'page' => $numberPage
        ]);

        $this->view->page = $paginator->getPaginate();
    }

//    If you are logged in, only execute the following (create new news page)

    public function NewAction()
    {

    }

    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "news",
                'action' => 'index'
            ]);

            return;
        }

        $news = new News();
        $news->users_id = $this->request->getPost("users_id");  // <---- must be deleted when you can log in!
        $news->name = $this->request->getPost("name");
        $news->message = $this->request->getPost("message");
        $news->status = 0;


        if (!$news->save()) {
            foreach ($news->getMessages() as $error) {
                $this->flash->error($error);
            }

            $this->dispatcher->forward([
                'controller' => "news",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("news was created successfully");

        $this->dispatcher->forward([
            'controller' => "news",
            'action' => 'index'
        ]);

        $this->view->disable();    // omdat we geen view willen aanmaken die daadwerkelijk post.
        $this->response->redirect(news);
    }

    public function manageAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'News', $_POST);
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $news = News::find();

        if (count($news) == 0) {
            $this->flash->notice("There is currently no news");

            $this->dispatcher->forward([
                "controller" => "news",
                "action" => "index"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $news,
            'limit'=> 10,
            'page' => $numberPage
        ]);

        $this->view->page = $paginator->getPaginate();
    }

    public function deleteAction($id)
    {
        $news = News::findFirstByid($id);
        if (!$news->delete()) {

            foreach ($news->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "news",
                'action' => 'index'
            ]);

            return;
        }

        $this->flash->success("news was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "news",
            'action' => "index"
        ]);
        $this->view->disable();    // omdat we geen view willen aanmaken die daadwerkelijk post.
        $this->response->redirect(news);
    }

}

