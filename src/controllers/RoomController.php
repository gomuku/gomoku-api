<?php

namespace Api\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use Api\Model\RoomModel;

/**
 * User controller.
 */
class RoomController extends AbstractController
{
    /**
     * Get list available room.
     *
     * @param \Slim\Http\Request  $req
     * @param \Slim\Http\Response $res
     * @return \Slim\Http\Response
     */
    public function read(Request $req, Response $res, array $args)
    {
        $roomId = isset($args['id']) ? $args['id'] : null;
        $rooms = $this->model('Room')->getEnabledRooms($roomId);

        return $res->withJson([
            'code' => 200,
            'status' => 'OK',
            'data' => $rooms,
        ]);
    }

    /**
     * join to room.
     *
     * @param \Slim\Http\Request  $req
     * @param \Slim\Http\Response $res
     * @return \Slim\Http\Response
     */
    public function join(Request $req, Response $res, array $args)
    {
        $resData = [
            'code' => 200,
            'status' => 'OK',
            'message' => 'Join to room successful.'
        ];
        $roomId = isset($args['id']) ? $args['id'] : null;
        $room = $this->model('Room')->getEnabledRooms($roomId);

        if (!$room) {
            // not fount the room
            $resData['code'] = 404;
            $resData['status'] = 'NG';
            $resData['message'] = 'Room with id=\'{$roomId}\' not found.';
            return $res->withStatus(404)->withJson($resData);
        }

        $body = (object) $req->getParsedBody();
        if ($body->type != 'player' && $body->type != 'viewer') {
            // bad request params
            $resData['code'] = 400;
            $resData['status'] = 'NG';
            $resData['message'] = 'Bad request parameter \'type\'.';
            return $res->withStatus(400)->withJson($resData);
        }

        $login = $this->get('loginInfo');
        $column = "{$body->type}_ids";
        $memberIds = $room->{$column};
        if (!in_array($login->id, $memberIds)) {
            $saveData = [ "$column" => json_encode($memberIds + [$login->id]) ];
            $saveOk = $this->model('Room')->updateRoom($room->id, $saveData);
            if (!$saveOk) {
                // couldn't save
                $resData['code'] = 500;
                $resData['status'] = 'NG';
                $resData['message'] = 'Updating data to database has failed.';
                return $res->withStatus(500)->withJson($resData);
            }
            return $res->withStatus(200)->withJson($resData);
        }
    }

    /**
     * leave to room.
     *
     * @param \Slim\Http\Request  $req
     * @param \Slim\Http\Response $res
     * @return \Slim\Http\Response
     */
    public function leave(Request $req, Response $res, array $args)
    {
        $roomId = isset($args['id']) ? $args['id'] : null;
        $room = $this->model('Room', function (RoomModel $model) use ($roomId) {
            return $model->table()->find($roomId);
        });

        if (!$room) {
            // not fount the room
            $resData = [
                'code' => 404,
                'status' => 'NG',
                'message' => 'Room with id=\'{$roomId}\' not found.',
            ];

            return $res->withStatus(404)->withJson($resData);
        }

        $login = $this->get('loginInfo');
        $playerIds = json_decode($room->player_ids);
        $viewerIds = json_decode($room->viewer_ids);

        $hasChange = false;
        if (($key = array_search($login->id, $viewerIds)) !== false) {
            $hasChange = true;
            unset($viewerIds[$key]);
            $room->viewer_ids = json_encode($viewerIds);
        }
        if (($key = array_search($login->id, $playerIds)) !== false) {
            $hasChange = true;
            unset($playerIds[$key]);
            $room->player_ids = json_encode($playerIds);
        }

        $resData = [
            'code' => 200,
            'status' => 'OK',
            'message' => 'Join to room successful.',
        ];

        if ($hasChange) {
            $saveOk = $this->model('Room', function (RoomModel $model) use ($room) {
                return $model->table()->update((array) $room);
            });
            if (!$saveOk) {
                // couldn't save
                $resData['code'] = 500;
                $resData['status'] = 'NG';
                $resData['message'] = 'Updating data to database has failed.';

                return $res->withStatus(500)->withJson($resData);
            }
        }

        return $res->withStatus(200)->withJson($resData);
    }
}
