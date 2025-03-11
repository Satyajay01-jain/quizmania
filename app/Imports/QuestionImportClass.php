<?php
// app/Imports/YourImportClass.php

namespace App\Imports;
use App\Models\Questions;

use Maatwebsite\Excel\Concerns\ToModel;

class QuestionImportClass implements ToModel
{
    public function model(array $row)
    {
        // Define how to create a model from the Excel row data
        return new Questions([
            'title_type' => $row[0],
            'title' => $row[1],
            'hi_title' => $row[2],
            'option_type' => $row[3],
            'option_count' => $row[4],
            'options' => $row[5],
            'category' => $row[6],
            // Add more columns as needed
        ]);
    }
}