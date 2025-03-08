<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin View</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
    <a href="<?php echo e(route('student.view')); ?>" style="border: solid black 1px">ดูรายชื่อ</a>
        <h1 class="text-2xl font-bold mb-4">รายชื่อนักศึกษา (จาก Google Sheets)</h1>
        
        <?php if(session('error')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="py-2 px-4 border-b">รหัสนักศึกษา</th>
                        <th class="py-2 px-4 border-b">ชื่อ</th>
                        <th class="py-2 px-4 border-b">นามสกุล</th>
                        <th class="py-2 px-4 border-b">สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-4 border-b"><?php echo e($student->std_id); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo e($student->std_firstname); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo e($student->std_lastname); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo e($student->std_status); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="py-2 px-4 border-b text-center">ไม่มีข้อมูลนักศึกษาที่ตรงกับ Google Sheets</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html><?php /**PATH D:\xampp\htdocs\Project7\resources\views/adminview.blade.php ENDPATH**/ ?>