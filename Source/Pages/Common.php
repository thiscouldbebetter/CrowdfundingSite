<?php

$configuration = include("../Configuration.php");

include("../Classes/License.php");
include("../Classes/LicenseTransferType.php");
include("../Classes/MathHelper.php");
include("../Classes/Notification.php");
include("../Classes/Order.php");
include("../Classes/Order_Product.php");
include("../Classes/PageWriter.php");
include("../Classes/PaymentClient.php");
include("../Classes/PaymentClientSquare.php");
include("../Classes/PersistenceClientMySQL.php");
include("../Classes/Product.php");
include("../Classes/Promotion.php");
include("../Classes/Promotion_Product.php");
include("../Classes/Session.php");
include("../Classes/User.php");
include("../Classes/WebClient.php");

Session::start($configuration);

?>
