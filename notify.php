<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>
    function checkNewRequests() {
        fetch('backend/check_new_requests.php')  
            .then(response => response.json())
            .then(data => {
                if (data.status === "success" && data.pending_count > 0) {
                    Swal.fire({
                        title: '📢 มีคำขอเช่าใหม่!',
                        text: 'มี ' + data.pending_count + ' คำขอเช่าที่รอการอนุมัติ',
                        icon: 'info',
                        confirmButtonText: 'ไปจัดการ',
                        showCancelButton: true,
                        cancelButtonText: 'ปิด'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'backend/manage_rent_requests.php';
                        }
                    });
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // loop check 10 sec eiei
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        setInterval(checkNewRequests, 10000);
    <?php endif; ?>

    function checknewtopup() {
        fetch('backend/check_topupnotify.php')  
            .then(response => response.json())
            .then(data => {
                if (data.status === "success" && data.pending_count > 0) {
                    Swal.fire({
                        title: '📢 มีคนเติมเงินเข้ามา!',
                        text: 'มี ' + data.pending_count + ' คำขอเติมเงิน',
                        icon: 'info',
                        confirmButtonText: 'ไปจัดการ',
                        showCancelButton: true,
                        cancelButtonText: 'ปิด'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'backend/topupbackend.php';
                        }
                    });
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // loop check 10 sec eiei
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        setInterval(checknewtopup, 10000);
    <?php endif; ?>

</script>


