<?php
session_start();

if (!isset($_SESSION['checkout_data'])) {
    header("Location: ./checkout.php");
    exit();
}

$data = $_SESSION['checkout_data'];
$data['pm'] = $_SESSION['payment_method'] ?? "cod";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm | RestroHub</title>
    <link rel="stylesheet" href="./styles/style.css">
    <link rel="stylesheet" href="./styles/responsive.css">
</head>
<body>

<?php require("./components/header.php"); ?>

<main>
    <section class="confirm_card text-center p-20 w-fit shadow border-curve">
        <div class="w-fit" style="margin: auto">
            <h1 class="heading">Confirm Order</h1>
            <hr>
        </div>

        <div class="p-20 checkout-details">
            <div class="flex gap mt-20">
                <p class="tal"><b>Name:</b></p>
                <p class="tar"><?php echo htmlspecialchars($data['name']); ?></p>
            </div>

            <div class="flex gap mt-20">
                <p class="tal"><b>Phone:</b></p>
                <p class="tar"><?php echo htmlspecialchars($data['phone']); ?></p>
            </div>

            <div class="flex gap mt-20">
                <p class="tal"><b>Address:</b></p>
                <p class="tar"><?php echo htmlspecialchars($data['address']); ?></p>
            </div>

            <div class="flex gap mt-20">
                <p class="tal"><b>Note:</b></p>
                <p class="tar"><?php echo htmlspecialchars($data['note'] ?? 'No note'); ?></p>
            </div>

            <div class="flex gap mt-20">
                <p class="tal"><b>Total Price:</b></p>
                <p class="tar">₱<?php echo number_format((float)($data['total_price'] ?? 0), 2); ?></p>
            </div>

            <div class="flex gap mt-20">
                <p class="tal"><b>Payment Method:</b></p>
                <p class="tar">Cash on Delivery</p>
            </div>

            <button class="button border-curve mt-20 cod_btn">Place Order</button>
        </div>
    </section>
</main>

<?php require "./components/footer.php"; ?>

<script>
const checkoutData = <?php echo json_encode($data); ?>;

document.querySelector('.cod_btn').addEventListener('click', () => {
    fetch('./backend/place-order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(checkoutData)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = './track-order.php';
        } else {
            alert(data.message);
        }
    })
    .catch(err => {
        alert("Order error. Please check backend/place-order.php");
        console.error(err);
    });
});
</script>

<script src="./js/app.js" type="module"></script>
</body>
</html>