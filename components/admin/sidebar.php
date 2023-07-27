<aside class="sidebar">
  <ul>
    <li class="title-dashboard <?php if ($current_page == 'admin.php') echo 'active'; ?>"><a href="admin.php"><i class="bx bxs-dashboard"></i>Dashboard</a></li>
    <br>
    <hr>
    </li>
    <!-- <li>
      <a href="#"><i class="bx bxs-cart-alt"></i>Ecommerce</a>
      <ul> -->
    <li <?php if ($current_page == 'admin_users.php') echo 'class="active"'; ?>><a href="admin_users.php"><i class="bx bxs-user"></i>Users</a></li>
    <li <?php if ($current_page == 'admin_view_products.php') echo 'class="active"'; ?>><a href="admin_view_products.php"><i class="bx bx-package"></i>Products</a></li>
    <li <?php if ($current_page == 'admin_menu.php') echo 'class="active"'; ?>><a href="admin_menu.php"><i class="bx bx-category"></i>Today's Menu</a></li>
    <li class="not-ready"><a href="not_found.php?page=admin_orders.php"><i class="bx bx-receipt"></i>Orders</a></li>
    <!-- </ul>
    </li> -->

    <!-- <div class="analytics">
      <a href="#"><i class="bx bx-line-chart"></i>Analytics</a>
      <li class="not-ready"><a href="#"><i class="bx bxs-pie-chart-alt-2"></i>Sales Chart</a></li>
      <li class="not-ready"><a href="#"><i class="bx bxs-pie-chart-alt-2"></i>Product Chart</a></li>
      <li class="not-ready"><a href="#"><i class="bx bx-bar-chart"></i>Statistics</a></li>

    </div> -->

    <li <?php if ($current_page == 'admin_analytics.php') echo 'class="active"'; ?>><a href="#"><i class="bx bx-line-chart"></i>Analytics</a></li>

    <li class="not-ready" style="text-decoration: line-through;"><a href="#"><i class="bx bx-envelope"></i>Mail</a></li>
    <li class="not-ready"><a href="#"><i class="bx bx-group"></i>Subscribers</a></li>
    <li class="not-ready"><a href="#"><i class="bx bx-cog"></i>Settings</a></li>
    <li <?php if ($current_page == 'help.php') echo 'class="active"'; ?>><a href="help.php"><i class="bx bx-info-circle"></i>Help & Support</a></li>
  </ul>
  <div class="logout">
    <a href="logout.php"><i class="bx bx-run" style="margin-left: 5px"></i>Logout</a>
  </div>
</aside>