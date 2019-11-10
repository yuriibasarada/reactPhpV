<?php


namespace App\Authentication;


use App\Core\JsonResponse;
use Exception;
use Firebase\JWT\ExpiredException;
use Psr\Http\Message\ServerRequestInterface;

final class GetIdController
{

    /**
     * @var JwtDecode
     */
    private $jwtDecode;

    public function __construct(JwtDecode $jwtDecode)
    {
        $this->jwtDecode = $jwtDecode;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $input = new Input($request);
        try {
            $id = $this->jwtDecode->getUserId($input->token())->id;
            return JsonResponse::ok(['id' => $id]);
        } catch (ExpiredException $e) {
            return JsonResponse::badRequest($e->getMessage());
        } catch (Exception $e) {
            return JsonResponse::internalServerError($e->getMessage());
        }

    }
}