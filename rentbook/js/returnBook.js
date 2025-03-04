function returnBook(rentalId) {
    Swal.fire({
        title: 'ยืนยันการคืนหนังสือ?',
        text: "คุณต้องการคืนหนังสือเล่มนี้หรือไม่?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch("return_book.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "rental_id=" + rentalId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById("rental_" + rentalId).remove();
                    Swal.fire(
                        'คืนหนังสือสำเร็จ!',
                        'หนังสือถูกคืนเรียบร้อยแล้ว!',
                        'success'
                    );
                } else {
                    Swal.fire(
                        'เกิดข้อผิดพลาด',
                        data.message || 'ไม่สามารถคืนหนังสือได้',
                        'error'
                    );
                }
            })
            .catch(error => {
                Swal.fire(
                    'เกิดข้อผิดพลาด',
                    'ไม่สามารถคืนหนังสือได้',
                    'error'
                );
                console.error("Error:", error);
            });
        }
    });
}


