<?php

function is_set_menu($conn, $user_data)
{

    // is set the daily menu?
    $query = "SELECT * FROM `daily_menu` WHERE `date` = CURDATE()";
    $stmt = $conn->prepare($query);

    if ($stmt->execute()) {
        $daily_menu = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$daily_menu) {
            $query_clear_cart = "DELETE FROM cart WHERE user_id = ?";
            $stmt_clear_cart = $conn->prepare($query_clear_cart);

            if ($stmt_clear_cart->execute([$user_data['id']])) {
            } else {
                $error_info = $stmt_clear_cart->errorInfo();
                $error_msg[] = "Error: " . $error_info[2];
            }
        } else {
            $query_products = "SELECT p.id FROM products AS p
                        JOIN daily_menu_items AS dmi ON dmi.product_id = p.id 
                        JOIN daily_menu AS dm ON dmi.daily_menu_id = dm.id
                        WHERE dm.date = CURDATE()";
            $stmt_products = $conn->prepare($query_products);
            $stmt_products->execute();
            $daily_products = $stmt_products->fetchAll(PDO::FETCH_ASSOC);
            if (count($daily_products) > 0) {
                $query_clear_cart = "DELETE FROM cart WHERE user_id = ? AND product_id != ?";
                $stmt_clear_cart = $conn->prepare($query_clear_cart);

                foreach ($daily_products as $product) {
                    if ($stmt_clear_cart->execute([$user_data['id'], $product['id']])) {
                    } else {
                        $error_info = $stmt_clear_cart->errorInfo();
                        $error_msg[] = "Error: " . $error_info[2];
                    }
                }
            }
        }
    } else {
        $error_info = $stmt->errorInfo();
        $error_msg = 'Error: ' . $error_info[2];
    }
}


function widget_query($conn, $table)
{
    try {
        $stmt = $conn->prepare("SELECT * FROM $table");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $num_of = count($result);
    } catch (PDOException $e) {
        $error_msg[] = "Eroare: " . $e->getMessage();
    } catch (Exception $e) {
        $error_msg[] = "Eroare: " . $e->getMessage();
    }
    return $num_of;
}
