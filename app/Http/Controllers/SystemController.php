<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller
{
    public function index()
    {
        // URL ของ Google Sheet ในรูปแบบ CSV
        $spreadsheetUrl = "https://docs.google.com/spreadsheets/d/1OOyDvfmecRd165wNMVJMSNIwxm6YtT03sozXGuDQcMc/export?format=csv&gid=0";

        // ดึงข้อมูลจาก Google Sheet
        $csvData = file_get_contents($spreadsheetUrl);

        // แปลง CSV string เป็น array
        $rows = array_map('str_getcsv', explode("\n", $csvData));

        // array สำหรับแปลงเดือนจากอังกฤษเป็นไทย
        $monthMap = [
            'january' => 'มกราคม',
            'february' => 'กุมภาพันธ์',
            'march' => 'มีนาคม',
            'april' => 'เมษายน',
            'may' => 'พฤษภาคม',
            'june' => 'มิถุนายน',
            'july' => 'กรกฎาคม',
            'august' => 'สิงหาคม',
            'september' => 'กันยายน',
            'october' => 'ตุลาคม',
            'november' => 'พฤศจิกายน',
            'december' => 'ธันวาคม'
        ];

        $filteredData = [];
        $uniqueRecords = []; // เก็บข้อมูลที่ไม่ซ้ำสำหรับตรวจสอบ

        foreach ($rows as $row) {
            if (isset($row[0]) && isset($row[2]) && isset($row[3]) && isset($row[4])) {
                // แยกวันที่และเวลาจาก column0 (เช่น "03 March 2025 01:56PM")
                $dateTimeParts = explode(' ', $row[0]);
                if (count($dateTimeParts) >= 4) {
                    $day = $dateTimeParts[1]; // วัน (03)
                    $month = strtolower($dateTimeParts[0]); // เดือน (march)
                    $year = $dateTimeParts[2]; // ปี (2025)
                    $timeWithAmPm = $dateTimeParts[4]; // เวลาพร้อม AM/PM (01:56PM)

                    // แยกเวลาและ AM/PM ด้วย regex
                    if (preg_match('/(\d{2}:\d{2})(AM|PM)/i', $timeWithAmPm, $matches)) {
                        $time = $matches[1]; // เวลา (01:56)
                        $amPm = strtoupper($matches[2]); // AM/PM (PM)
                    } else {
                        $time = $timeWithAmPm; // ใช้ทั้งหมด (เช่น 01:56)
                        $amPm = ''; // ค่าเริ่มต้นถ้าไม่มี AM/PM
                    }

                    // แปลงเดือนเป็นไทย
                    $thaiMonth = isset($monthMap[$month]) ? $monthMap[$month] : $month;

                    // แปลงปี ค.ศ. เป็น พ.ศ.
                    $thaiYear = (int)$year + 543;

                    // ดึงชั่วโมงและนาที
                    $hourMinute = explode(':', $time);
                    $hour = isset($hourMinute[0]) ? (int)$hourMinute[0] : 0; // ชั่วโมง
                    $minute = isset($hourMinute[1]) ? (int)$hourMinute[1] : 0; // นาที

                    // ปรับชั่วโมงตาม AM/PM
                    if ($amPm === 'PM' && $hour < 12) {
                        $hour += 12; // แปลงเป็น 24 ชั่วโมง
                    } elseif ($amPm === 'AM' && $hour == 12) {
                        $hour = 0; // เที่ยงคืน
                    }

                    // กำหนดช่วงเวลา (เช้า หรือ บ่าย)
                    $period = ($hour < 12) ? 'เช้า' : 'บ่าย';

                    // จัดรูปแบบเวลา
                    $thaiTime = sprintf("%02d:%02d %s", $hour, $minute, $period);

                    // รวมวันที่ในรูปแบบไทย
                    $thaiDate = "$day $thaiMonth $thaiYear $thaiTime";

                    // สร้าง key สำหรับตรวจสอบข้อมูลซ้ำ (วัน + รหัสนักศึกษา + ชื่อ + นามสกุล)
                    $uniqueKey = "$day $thaiMonth $thaiYear-{$row[2]}-{$row[3]}-{$row[4]}";

                    // ตรวจสอบว่ามีข้อมูลนี้ในช่วงเวลาเดียวกันหรือไม่
                    if (!isset($uniqueRecords[$uniqueKey])) {
                        // ถ้ายังไม่มีข้อมูลนี้เลย เพิ่มเข้าไป
                        $uniqueRecords[$uniqueKey] = [$amPm];
                        $filteredData[] = [
                            'column0' => $thaiDate,
                            'column2' => $row[2],    // รหัสนักศึกษา
                            'column3' => $row[3],    // ชื่อ
                            'column4' => $row[4]     // นามสกุล
                        ];
                    } else {
                        // ถ้ามีข้อมูลนี้แล้ว ตรวจสอบ AM/PM
                        if (!in_array($amPm, $uniqueRecords[$uniqueKey])) {
                            // ถ้าเป็น AM/PM ที่ต่างจากที่มีอยู่แล้ว เพิ่มเข้าไป
                            $uniqueRecords[$uniqueKey][] = $amPm;
                            $filteredData[] = [
                                'column0' => $thaiDate,
                                'column2' => $row[2],
                                'column3' => $row[3],
                                'column4' => $row[4]
                            ];
                        }
                        // ถ้า AM/PM ซ้ำกับที่มีอยู่แล้ว จะไม่เพิ่มข้อมูล
                    }
                }
            }
        }

        // ส่งข้อมูลไปยัง view 'welcome'
        return view('welcome', ['data' => $filteredData]);
    }

    public function adminview()
    {
        // URL ของ Google Sheets ที่เป็น CSV
        $csvUrl = 'https://docs.google.com/spreadsheets/d/1OOyDvfmecRd165wNMVJMSNIwxm6YtT03sozXGuDQcMc/tq?tqx=out:csv&sheet=Sheet1';

        try {
            // ดึงข้อมูลจาก URL และแปลง CSV เป็น array
            $rows = array_map('str_getcsv', explode("\n", Http::get($csvUrl)->body()));

            // ตรวจสอบว่าข้อมูลถูกดึงมาได้หรือไม่
            if (empty($rows)) {
                throw new \Exception('ไม่พบข้อมูลใน Google Sheets');
            }

            // ลบแถวแรก (header) ถ้ามี
            $sheetData = array_slice($rows, 1);

            // ตรวจสอบว่ามีข้อมูลใน $sheetData หรือไม่
            if (empty($sheetData)) {
                throw new \Exception('ไม่พบข้อมูลหลังจากลบ header');
            }

            // ดึงข้อมูลนักเรียนทั้งหมดจากตาราง students ในฐานข้อมูล studentdata
            // ใช้ keyBy('st_id') เพื่อสร้าง associative array โดยใช้ st_id เป็น key
            $students = DB::table('students')->get()->keyBy('std_id');

            // ตรวจสอบว่ามีข้อมูลในตาราง students หรือไม่
            if ($students->isEmpty()) {
                throw new \Exception('ไม่พบข้อมูลนักเรียนในตาราง students');
            }

            // ตัวแปรเก็บนักเรียนที่ตรงกับ st_id ใน Google Sheets
            $matchedStudents = [];

            // เก็บข้อมูล row[2] ทั้งหมดเพื่อ debug (ถ้าต้องการ)
            $row2Values = [];

            // ตรวจสอบแต่ละแถวใน Google Sheets
            foreach ($sheetData as $row) {
                // เก็บค่า row[2] เพื่อ debug
                if (isset($row[2])) {
                    $row2Values[] = trim($row[2]);
                }

                // ตรวจสอบว่าคอลัมน์ที่ 2 (index 2) มีค่าและไม่ว่าง
                if (isset($row[2]) && !empty($row[2])) {
                    $stId = trim($row[2]); // ลบช่องว่างรอบๆ เพื่อความแม่นยำ
                    // ตรวจสอบว่าพบ st_id ที่ตรงกันในตาราง students หรือไม่
                    if (isset($students[$stId])) {
                        // หากพบ st_id ที่ตรงกันในตาราง students
                        $matchedStudents[] = $students[$stId];
                    }
                }
            }

            // ถ้าต้องการดูข้อมูล row[2] ทั้งหมด (สำหรับ debug)
            // dd($row2Values);

            // ตรวจสอบว่ามีนักเรียนที่ตรงกันหรือไม่
            if (empty($matchedStudents)) {
                throw new \Exception('ไม่พบนักเรียนที่ตรงกับข้อมูลใน Google Sheets');
            }

            // ส่งข้อมูลไปที่ view
            return view('adminview', [
                'students' => $matchedStudents
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการดึงข้อมูล: ' . $e->getMessage());
        }
    }
    public function createStudent(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
        ]);
        DB::table('students')->insert([
            'std_id' => 'ST' . time(), // สร้างรหัสนักศึกษาแบบอัตโนมัติ (เช่น ST1698765432)
            'std_firstname' => $request->firstname,
            'std_lastname' => $request->lastname,
            'std_status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'เพิ่มชื่อนักศึกษาสำเร็จ');
    }

    public function studentview()
    {
        $students = DB::table('students')->get(); // ดึงข้อมูลจากตาราง students
        return view('studentview', ['students' => $students]);
    }
}
