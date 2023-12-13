<?php

namespace  App\Service;

class ZutEduAPI {
    private const API_BASE = "https://plan.zut.edu.pl/schedule_student.php";

    public function __construct() {}
    public function getMeetingData(string $id, $now) : array {
        $json = $this->getAPIJson($id, $now);

        return json_decode($json, true);
    }

    private function getAPIJson(string $id, $now) : string {
        $startDate = clone $now;
        $endDate = (clone $now)->modify("+2 days");

        $query_arr = array (
            'room' => $id,
            'start' => $startDate->format("Y-m-d")."T".$startDate->format("H:i:s"),
            'end' => $endDate->format("Y-m-d")."T".$endDate->format("H:i:s")
        );

        $query = http_build_query($query_arr);

        return file_get_contents(self::API_BASE.'?'.$query);
    }
}