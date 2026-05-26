<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Order food online through RestroHub.">
    <meta name="theme-color" content="#F7922F">

    <link rel="shortcut icon" href="./images/logo.png" type="image/x-icon">

    <title>Menu | RestroHub</title>

    <link rel="stylesheet" href="./styles/style.css">
    <link rel="stylesheet" href="./styles/responsive.css">
</head>

<body>

<?php require("./components/header.php"); ?>

<?php
require_once("./api_config.php");

$api = $API_BASE . "products.php";
$response = @file_get_contents($api);

$foods = [];

if ($response !== false) {
    $data = json_decode($response, true);

    if (isset($data['success']) && $data['success'] === true && isset($data['products'])) {
        $foods = $data['products'];
    }
}

if (isset($_GET['search']) && $_GET['search'] != "") {
    $searchKey = strtolower($_GET['search']);

    $foods = array_filter($foods, function ($food) use ($searchKey) {
        return strpos(strtolower($food['name']), $searchKey) !== false;
    });
}

if (isset($_GET['veg']) && $_GET['veg'] != "all") {
    $veg = $_GET['veg'];

    $foods = array_filter($foods, function ($food) use ($veg) {
        return isset($food['veg']) && $food['veg'] == $veg;
    });
}
?>

<img src="./images/ic_filter.svg" alt="filter icon" class="menu_filter_icon">

<aside class="sidebar menu_sidebar shadow p_7-20">

    <h4 class="heading">Filter</h4>

    <div class="mt-20">
        <input type="radio" onchange="vegFilter('all')" class="cbox-veg_nonveg" name="veg-filter" value="all" id="all"
            <?php if (!isset($_GET['veg']) || $_GET['veg'] == "all") echo "checked"; ?>>
        <label for="all"> All</label>
    </div>

    <div style="margin-top:10px">
        <input type="radio" onchange="vegFilter('Veg')" class="cbox-veg_nonveg" name="veg-filter" value="Veg" id="veg"
            <?php if (isset($_GET['veg']) && $_GET['veg'] == "Veg") echo "checked"; ?>>
        <label for="veg"> Veg</label>
    </div>

    <div style="margin-top:10px">
        <input type="radio" onchange="vegFilter('Non-Veg')" class="cbox-veg_nonveg" name="veg-filter" value="Non-Veg" id="non-veg"
            <?php if (isset($_GET['veg']) && $_GET['veg'] == "Non-Veg") echo "checked"; ?>>
        <label for="non-veg"> Non-Veg</label>
    </div>

</aside>

<main class="menu_container">

    <div class="menu_container_inner">

        <button class="go_top no_bg no_outline">
            <img src="./images/ic_top.svg" alt="go to top">
        </button>

        <section class="food_categories">

            <div class="flex items-center">
                <h2 class="food_category-title heading">Top Categories</h2>
                <button class="toggle_categories button">View all</button>
            </div>

            <div class="category_container mt-20 flex gap wrap justify-center">

                <div class="food_category">
                    <button class="text-center pointer no_bg no_outline"
                            onclick="window.location.href='./menu.php?veg=all'">

                        <img src="./images/all.jpg"
                             class="border-curve food_category-img"
                             alt="all food">

                        <p class="food_category-name">All</p>
                    </button>
                </div>

            </div>

        </section>

        <div class="menu_food-card-category-container">

            <section class="menu_food-card-container mt-20 flex direction-col">

                <h2 class="heading h2">
                    <?php
                    echo isset($_GET['search']) && $_GET['search'] != ""
                        ? "Search: " . htmlspecialchars($_GET['search'])
                        : "Foods";
                    ?>
                </h2>

                <div class="food_cards mt-20 flex gap wrap justify-start">

                    <?php if (count($foods) > 0): ?>

                        <?php foreach ($foods as $food): ?>

                            <?php
                            if (isset($food['disabled']) && $food['disabled'] == 1) {
                                continue;
                            }

                            $foodId = isset($food['f_id']) ? $food['f_id'] : $food['Id'];

                            $img = "";

                            if (isset($food['img']) && !empty($food['img'])) {
                                $img = $food['img'];
                            } elseif (isset($food['image']) && !empty($food['image'])) {
                                $img = $food['image'];
                            } else {
                                $img = "https://via.placeholder.com/300x200.png?text=Food";
                            }

                            if (strpos($img, "http://") === 0 || strpos($img, "https://") === 0) {
                                $imgSrc = $img;
                            } else {
                                $imgSrc = "./uploads/foods/" . $img;
                            }

                            $addToCartLink = "./backend/add-to-cart.php?f_id=" . urlencode($foodId);
                            ?>

                            <div class="menu_food-card border-curve shadow">

                                <p class="card__tag text-center heading">
                                    <?php echo isset($food['veg']) ? htmlspecialchars($food['veg']) : "Veg"; ?>
                                </p>

                                <div class="card__food-img">
                                    <a href="<?php echo $addToCartLink; ?>">
                                        <img src="<?php echo htmlspecialchars($imgSrc); ?>"
                                             alt="food image"
                                             class="border-curve food_img">
                                    </a>
                                </div>

                                <article class="card__food-info flex items-center">

                                    <a href="<?php echo $addToCartLink; ?>">
                                        <h2 class="card__food-title heading">
                                            <?php echo htmlspecialchars($food['name']); ?>
                                        </h2>
                                    </a>

                                    <p class="card__food-price heading">
                                        Rs. <?php echo htmlspecialchars($food['price']); ?>
                                    </p>

                                </article>

                                <p class="card__food-desc">
                                    <?php
                                    echo isset($food['short_desc'])
                                        ? htmlspecialchars($food['short_desc'])
                                        : htmlspecialchars($food['name']);
                                    ?>
                                </p>

                                <div class="card__btns flex">
                                    <form action="./backend/add-to-cart.php" method="post" class="form_food-card">
                                        <input type="hidden" name="f_id" value="<?php echo htmlspecialchars($foodId); ?>">

                                        <button type="submit"
                                                class="button card__btn btn_add-to-cart flex justify-center border-curve">
                                            <img src="./images/ic_add-cart.svg" alt="add to cart">
                                        </button>
                                    </form>
                                </div>

                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <h2>No foods found or API is offline.</h2>

                    <?php endif; ?>

                </div>

            </section>

        </div>

    </div>

    <?php require("./components/footer.php"); ?>

</main>

<script>
function vegFilter(key)
{
    let searchKey = "<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>";

    if (searchKey !== "") {
        window.location.href = "./menu.php?search=" + searchKey + "&veg=" + key;
    } else {
        window.location.href = "./menu.php?veg=" + key;
    }
}
</script>

<script type="module" src="./js/app.js"></script>

</body>
</html>