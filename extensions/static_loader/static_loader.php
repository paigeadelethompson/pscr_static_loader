<?php
/**
 * Created by PhpStorm.
 * User: erratic
 * Date: 7/10/2018
 * Time: 11:23 PM
 */

namespace pscr\extensions\static_loader;

use pscr\lib\exceptions\invalid_argument_exception;
use pscr\lib\exceptions\not_implemented_exception;
use pscr\lib\http\response;
use pscr\lib\logging\logger;
use pscr\lib\model\i_content_renderer;

/**
 * Class static_loader
 * @package pscr\extensions\static_loader
 */
class static_loader implements i_content_renderer
{

    /**
     * @var
     */
    private $request;
    /**
     * @var
     */
    private $response;

    /**
     * @param $request
     * @return mixed|void
     */
    function set_request($request)
    {
        $this->request = $request;
    }

    /**
     * @return mixed
     */
    function render()
    {
        $local_path = $this->request->get_selected_route_entry_path_name();
        $file = $this->request->get_selected_route_match();
        // sanitize the input a little
        $file = str_replace("..", "", $file);
        $full_path = $local_path . $file;

        if(file_exists($full_path)) {
            $this->response->set_header('Content-Type', $this->request->get_selected_route_content_type());
            $this->response->set_response_body(file_get_contents($full_path));
            return $this->response;
        }
        else {
            logger::_()->info($this, "requested static file not found", $full_path);
            $this->response->set_header('Location', "/404");
            return $this->response;
        }
    }

    /**
     * @return mixed
     */
    function render_to_response()
    {
        $this->response = new response($this->request);
        return $this->render();
    }
}