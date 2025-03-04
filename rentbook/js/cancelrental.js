function cancelRental(rentalId) {
    Swal.fire({
        title: 'ยืนยันการยกเลิก',
        text: "คุณต้องการยกเลิกการเช่าหนังสือเล่มนี้หรือไม่?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ใช่',
        cancelButtonText: 'ไม่'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch("cancel_rental.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "rental_id=" + rentalId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById("rental_" + rentalId).remove();
                    Swal.fire(
                        'ยกเลิกสำเร็จ!',
                        'การเช่าถูกยกเลิกเรียบร้อยแล้ว!',
                        'success'
                    );
                } else {
                    Swal.fire(
                        'เกิดข้อผิดพลาด',
                        data.message || 'ไม่สามารถยกเลิกการเช่าได้',
                        'error'
                    );
                }
            })
            .catch(error => {
                Swal.fire(
                    'เกิดข้อผิดพลาด',
                    'ไม่สามารถยกเลิกการเช่าได้',
                    'error'
                );
                console.error("Error:", error);
            });
        }
    });
}
