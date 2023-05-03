<?php

    namespace App\Response;

    class Response {

        public function invalid($data = [], $status_code) {
            return response()->json([
                'message' => 'Invalid '. $data,
            ], $status_code);
        }

        public function not_found($data = [], $status_code) {
            return response()->json([
                'message' => 'Not Found '. $data,
            ],  $status_code);
        }

        public function success($message, $status_code) {
            return response()->json([
                'message' => $message,
            ], $status_code);
        }

    }

?>