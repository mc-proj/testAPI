<?php
namespace App\Controller;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;

class GetCountController {

    public function __construct(private UserRepository $userRepository) {
        //
    }

    /*public function __invoke(Request $request): int {
        
        $onlineQuery = $request->get('online');
        $conditions = [];
        if($onlineQuery !== null) {
            $conditions = ['online' => $onlineQuery === '1' ? true : false];
        }
        return $this->userRepository->count($conditions);
    }*/

    public function __invoke($data): int {
        return count($data);
    }
}