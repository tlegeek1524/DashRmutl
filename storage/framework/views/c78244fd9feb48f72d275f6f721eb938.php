<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>student View</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-4">รายชื่อนักศึกษา</h1>
        
        <?php if(session('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

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
                            <td colspan="4" class="py-2 px-4 border-b text-center">ไม่มีข้อมูลนักศึกษา</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- แบบฟอร์มเพิ่มชื่อนักศึกษา -->
        <h2 class="text-xl font-bold mt-8 mb-4">เพิ่มชื่อนักศึกษา</h2>
        <form action="<?php echo e(route('createstudent')); ?>" method="get">
            <input type="text" name="firstname" id="firstname" placeholder="ชื่อ" class="border p-2 rounded mr-2">
            <input type="text" name="lastname" id="lastname" placeholder="นามสกุล" class="border p-2 rounded mr-2">
            <button type="submit" class="bg-blue-500 text-white p-2 rounded">เพิ่มชื่อนักศึกษา</button>
        </form>
        <a href="<?php echo e(route('admin.view')); ?>" class="mt-4 inline-block bg-green-500 text-white p-2 rounded">ดูรายชื่อ</a>
    </div>
    
</body>
</html><?php /**PATH D:\xampp\htdocs\Project7\resources\views/studentview.blade.php ENDPATH**/ ?>