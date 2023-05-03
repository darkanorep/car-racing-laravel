<?php

    namespace App\Response;

    class Response {

        public static function invalid($data = [], $status_code) {
            return response()->json([
                'message' => 'Invalid '. $data,
            ], $status_code);
        }

        public static function not_found($data = []) {
            return response()->json([
                'message' => $data . ' not found',
            ],  Status::NOT_FOUND);
        }

        public static function success($message) {
            return response()->json([
                'message' => $message,
            ], Status::OK);
        }

        public static function forbidden($message) {
            return response()->json([
                'message' => $message,
            ], Status::FORBIDDEN);
        }

        public static function not_acceptable($message) {
            return response()->json([
                'message' => $message,
            ], Status::NOT_ACCEPTABLE);
        }

        public static function request_timeout($message) {
            return response()->json([
                'message' => $message,
            ], Status::REQUEST_TIMEOUT);
        }

    }

?>