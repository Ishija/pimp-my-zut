<?php

namespace  App;

class ZutEduAPI {
    private const API_BASE = "https://plan.zut.edu.pl/schedule_student.php";

    public function __construct() {}
    public function getClassData(string $id) : array {
        $json = $this->getAPIJson($id);

        return json_decode($json, true);
    }

    public function getAPIJson(string $id) : String {
        $startDate = $this->getStartDate();
        $endDate = $this->getStartDate()->modify("+2 hours");

        $query_arr = array (
            'room' => $id,
            'start' => $startDate->format("Y-m-d")."T".$startDate->format("H:i:s"),
            'end' => $endDate->format("Y-m-d")."T".$endDate->format("H:i:s")
        );

        $query = http_build_query($query_arr);

        return file_get_contents(self::API_BASE.'?'.$query);
    }

    private function getStartDate() : \DateTime { //TODO different start date finding??
        $now = new \DateTime();

        return $now;
    }
}