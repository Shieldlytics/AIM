<?php 

function getConnection() {
    $host = "pena-cloud.network";
    $port = 19307;
    $databaseName = "AIM";
    $username = "ErnestPenaJr";
    $password = "$268RedDragons";
    $dsn = "mysql:host=$host;port=$port;dbname=$databaseName;charset=utf8mb4";
    try {
        $conn = new PDO($dsn, $username, $password);
        // Set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
    return $conn;    

}

// function getConnection(){
//     $DB_DNS = "mysql:host=localhost;dbname=AIM;charset=utf8mb4";
//     $DB_USER = "ErnestPenaJr";
//     $DB_PASSWORD = "$268RedDragons";
//     $conn = new PDO($DB_DNS, $DB_USER, $DB_PASSWORD);
//     $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//     return $conn;
// }

if(isset($_POST["method"])) {
    $method = $_POST["method"];
    if($method=="loadStateSales") {loadStateData();};
    //if the method is getByRiskLevel, then get the risk level from the post request
    if(isset($_POST["riskLevel"])){
        $risklevel = $_POST["riskLevel"];
        if($method=="getByRiskLevel") {getByLevel($risklevel);};
        if($method=="getVendorProductCount") {getVendorProductCount($risklevel);};
    }
    //if the method is getByVendor, then get the vendor name from the post request
    if(isset($_POST["vendorName"])){
        $vendorName = $_POST["vendorName"];
        if($method=="getByVendor") {getByVendor($vendorName);};
        if($method=="getVendorProductCountByName") {getProductCountByName($vendorName);};
    }
    

   
} else {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Invalid method detected"]);
}

function getByLevel($risklevel) {
    $risklevel = !empty($risklevel) ? $risklevel : " ";
    $pdo = getConnection(); // Assume this returns a PDO connection
    // if $risklevel is empty, then return all records
    if($risklevel == " ") {
        $sql = "SELECT vd.VENDOR_ID,vd.VENDOR_NAME,vd.PRODUCT_NAME,vd.STREET_NAME,vd.CITY,vd.STATE,vd.ZIP_CODE,vd.SELLER_FIRST_NAME,vd.SELLER_LAST_NAME,vd.SELLER_PHONE,vd.SELLER_EMAIL,vd.SELLER_URL,vd.SELLER_NAME_CHANGE,vd.ARTICLE_FINDING,vd.ARTICLE_URL,vd.PRODUCT_GATEGORY,vd.ANNUAL_SALES,vd.VERIFIED_COMPANY,vd.PRICE_DIFFERANCE,vd.PRODUCT_PRICE,vd.DIFFRENT_ADDRESS,COALESCE(r.RATING_SCORE,0) AS RATING_SCORE,r.score_category,COALESCE(pd.Product_Diversity_Score,0) AS Product_Diversity_Score,COALESCE(pd.VERIFIED_COMPANY_SCORE,0) AS VERIFIED_COMPANY_SCORE, COALESCE(pd.TOTAL_SCORE,0) AS TOTAL_SCORE FROM VENDOR_DETAILS vd LEFT JOIN (SELECT VENDOR_NAME, COUNT(*) AS RATING_SCORE, CASE WHEN COUNT(*) >= 60 THEN 'TOP' WHEN COUNT(*) BETWEEN 50 AND 59 THEN 'HIGH' WHEN COUNT(*) BETWEEN 40 AND 49 THEN 'MODERATE' WHEN COUNT(*) <= 39 THEN 'LOW' END AS SCORE_CATEGORY FROM VENDOR_DETAILS GROUP BY VENDOR_NAME) AS r ON vd.VENDOR_NAME = r.VENDOR_NAME LEFT JOIN (SELECT VENDOR_NAME,COUNT(DISTINCT PRODUCT_NAME) AS PRODUCT_DIVERSITY_SCORE,MAX(CASE WHEN VERIFIED_COMPANY = 0 THEN 10 ELSE 0 END) AS Verified_Company_Score,COUNT(DISTINCT PRODUCT_NAME) + MAX(CASE WHEN VERIFIED_COMPANY = 0 THEN 10 ELSE 0 END) AS Total_Score FROM VENDOR_DETAILS GROUP BY VENDOR_NAME) AS pd ON vd.VENDOR_NAME = pd.VENDOR_NAME ORDER BY COALESCE(r.RATING_SCORE, 0) DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $json = json_encode(array('items' => $result));
        echo $json;
        return;
    }else{
        $sql = "SELECT vd.VENDOR_ID,vd.VENDOR_NAME,vd.PRODUCT_NAME,vd.STREET_NAME,vd.CITY,vd.STATE,vd.ZIP_CODE,vd.SELLER_FIRST_NAME,vd.SELLER_LAST_NAME,vd.SELLER_PHONE,vd.SELLER_EMAIL,vd.SELLER_URL,vd.SELLER_NAME_CHANGE,vd.ARTICLE_FINDING,vd.ARTICLE_URL,vd.PRODUCT_GATEGORY,vd.ANNUAL_SALES,vd.VERIFIED_COMPANY,vd.PRICE_DIFFERANCE,vd.PRODUCT_PRICE,vd.DIFFRENT_ADDRESS,COALESCE(r.RATING_SCORE,0) AS RATING_SCORE,r.score_category,COALESCE(pd.Product_Diversity_Score,0) AS Product_Diversity_Score,COALESCE(pd.VERIFIED_COMPANY_SCORE,0) AS VERIFIED_COMPANY_SCORE, COALESCE(pd.TOTAL_SCORE,0) AS TOTAL_SCORE FROM VENDOR_DETAILS vd LEFT JOIN (SELECT VENDOR_NAME, COUNT(*) AS RATING_SCORE, CASE WHEN COUNT(*) >= 60 THEN 'TOP' WHEN COUNT(*) BETWEEN 50 AND 59 THEN 'HIGH' WHEN COUNT(*) BETWEEN 40 AND 49 THEN 'MODERATE' WHEN COUNT(*) <= 39 THEN 'LOW' END AS SCORE_CATEGORY FROM VENDOR_DETAILS GROUP BY VENDOR_NAME) AS r ON vd.VENDOR_NAME = r.VENDOR_NAME LEFT JOIN (SELECT VENDOR_NAME,COUNT(DISTINCT PRODUCT_NAME) AS PRODUCT_DIVERSITY_SCORE,MAX(CASE WHEN VERIFIED_COMPANY = 0 THEN 10 ELSE 0 END) AS Verified_Company_Score,COUNT(DISTINCT PRODUCT_NAME) + MAX(CASE WHEN VERIFIED_COMPANY = 0 THEN 10 ELSE 0 END) AS Total_Score FROM VENDOR_DETAILS GROUP BY VENDOR_NAME) AS pd ON vd.VENDOR_NAME = pd.VENDOR_NAME WHERE r.score_category = :risklevel ORDER BY COALESCE(r.RATING_SCORE, 0) DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':risklevel', $risklevel);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $json = json_encode(array('items' => $result));
        echo $json;
    }
}
function getByVendor($vendorName) {
    $vendorName = !empty($vendorName) ? $vendorName : " ";
    $pdo = getConnection();
    $sql = "SELECT vd.VENDOR_ID,vd.VENDOR_NAME,vd.PRODUCT_NAME,vd.STREET_NAME,vd.CITY,vd.STATE,vd.ZIP_CODE,vd.SELLER_FIRST_NAME,vd.SELLER_LAST_NAME,vd.SELLER_PHONE,vd.SELLER_EMAIL,vd.SELLER_URL,vd.SELLER_NAME_CHANGE,vd.ARTICLE_FINDING,vd.ARTICLE_URL,vd.PRODUCT_GATEGORY,vd.ANNUAL_SALES,vd.VERIFIED_COMPANY,vd.PRICE_DIFFERANCE,vd.PRODUCT_PRICE,vd.DIFFRENT_ADDRESS,COALESCE(r.RATING_SCORE,0) AS RATING_SCORE,r.score_category,COALESCE(pd.Product_Diversity_Score,0) AS Product_Diversity_Score,COALESCE(pd.VERIFIED_COMPANY_SCORE,0) AS VERIFIED_COMPANY_SCORE, COALESCE(pd.TOTAL_SCORE,0) AS TOTAL_SCORE FROM VENDOR_DETAILS vd LEFT JOIN (SELECT VENDOR_NAME, COUNT(*) AS RATING_SCORE, CASE WHEN COUNT(*) >= 60 THEN 'TOP' WHEN COUNT(*) BETWEEN 50 AND 59 THEN 'HIGH' WHEN COUNT(*) BETWEEN 40 AND 49 THEN 'MODERATE' WHEN COUNT(*) <= 39 THEN 'LOW' END AS SCORE_CATEGORY FROM VENDOR_DETAILS GROUP BY VENDOR_NAME) AS r ON vd.VENDOR_NAME = r.VENDOR_NAME LEFT JOIN (SELECT VENDOR_NAME,COUNT(DISTINCT PRODUCT_NAME) AS PRODUCT_DIVERSITY_SCORE,MAX(CASE WHEN VERIFIED_COMPANY = 0 THEN 10 ELSE 0 END) AS Verified_Company_Score,COUNT(DISTINCT PRODUCT_NAME) + MAX(CASE WHEN VERIFIED_COMPANY = 0 THEN 10 ELSE 0 END) AS Total_Score FROM VENDOR_DETAILS GROUP BY VENDOR_NAME) AS pd ON vd.VENDOR_NAME = pd.VENDOR_NAME  WHERE vd.VENDOR_NAME = :vendorName ORDER BY COALESCE(r.RATING_SCORE, 0) DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':vendorName', $vendorName);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $json = json_encode(array('items' => $result));
    echo $json;
}
function getVendorProductCount($risklevel) {
    $pdo = getConnection(); // Assume this returns a PDO connection
    if($risklevel == " ") {
        $sql = "SELECT R.SCORE_CATEGORY,COUNT(DISTINCT VD.VENDOR_ID) AS DISTINCT_VENDOR_COUNT,COUNT(DISTINCT VD.PRODUCT_NAME) AS DISTINCT_PRODUCT_COUNT FROM VENDOR_DETAILS VD JOIN (SELECT VENDOR_NAME, CASE WHEN COUNT(*) >= 60 THEN 'TOP' WHEN COUNT(*) BETWEEN 50 AND 59 THEN 'HIGH' WHEN COUNT(*) BETWEEN 40 AND 49 THEN 'MODERATE' WHEN COUNT(*) <= 39 THEN 'LOW' END AS SCORE_CATEGORY FROM VENDOR_DETAILS GROUP BY VENDOR_NAME) AS R ON VD.VENDOR_NAME = R.VENDOR_NAME GROUP BY R.SCORE_CATEGORY ORDER BY DISTINCT_VENDOR_COUNT DESC, DISTINCT_PRODUCT_COUNT DESC";
        $stmt = $pdo->prepare($sql);
    }else{
        $sql = "SELECT R.SCORE_CATEGORY,COUNT(DISTINCT VD.VENDOR_ID) AS DISTINCT_VENDOR_COUNT,COUNT(DISTINCT VD.PRODUCT_NAME) AS DISTINCT_PRODUCT_COUNT FROM VENDOR_DETAILS VD JOIN (SELECT VENDOR_NAME, CASE WHEN COUNT(*) >= 60 THEN 'TOP' WHEN COUNT(*) BETWEEN 50 AND 59 THEN 'HIGH' WHEN COUNT(*) BETWEEN 40 AND 49 THEN 'MODERATE' WHEN COUNT(*) <= 39 THEN 'LOW' END AS SCORE_CATEGORY FROM VENDOR_DETAILS GROUP BY VENDOR_NAME) AS R ON VD.VENDOR_NAME = R.VENDOR_NAME WHERE SCORE_CATEGORY = :risklevel GROUP BY R.SCORE_CATEGORY ORDER BY DISTINCT_VENDOR_COUNT DESC, DISTINCT_PRODUCT_COUNT DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':risklevel', $risklevel);
    }
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $json = json_encode(array('items' => $result));
    echo $json;
}
function getProductCountByName($vendorName) {
    $pdo = getConnection(); // Assume this returns a PDO connection
    if($vendorName == " ") {
        $sql = "SELECT R.SCORE_CATEGORY,COUNT(DISTINCT VD.VENDOR_ID) AS DISTINCT_VENDOR_COUNT,COUNT(DISTINCT VD.PRODUCT_NAME) AS DISTINCT_PRODUCT_COUNT FROM VENDOR_DETAILS VD JOIN (SELECT VENDOR_NAME, CASE WHEN COUNT(*) >= 60 THEN 'TOP' WHEN COUNT(*) BETWEEN 50 AND 59 THEN 'HIGH' WHEN COUNT(*) BETWEEN 40 AND 49 THEN 'MODERATE' WHEN COUNT(*) <= 39 THEN 'LOW' END AS SCORE_CATEGORY FROM VENDOR_DETAILS GROUP BY VENDOR_NAME) AS R ON VD.VENDOR_NAME = R.VENDOR_NAME GROUP BY R.SCORE_CATEGORY ORDER BY DISTINCT_VENDOR_COUNT DESC, DISTINCT_PRODUCT_COUNT DESC";
        $stmt = $pdo->prepare($sql);
    }else{
        $sql = "SELECT R.SCORE_CATEGORY,COUNT(DISTINCT VD.VENDOR_ID) AS DISTINCT_VENDOR_COUNT,COUNT(DISTINCT VD.PRODUCT_NAME) AS DISTINCT_PRODUCT_COUNT FROM VENDOR_DETAILS VD JOIN (SELECT VENDOR_NAME, CASE WHEN COUNT(*) >= 60 THEN 'TOP' WHEN COUNT(*) BETWEEN 50 AND 59 THEN 'HIGH' WHEN COUNT(*) BETWEEN 40 AND 49 THEN 'MODERATE' WHEN COUNT(*) <= 39 THEN 'LOW' END AS SCORE_CATEGORY FROM VENDOR_DETAILS WHERE VENDOR_NAME = :vendorName GROUP BY VENDOR_NAME) AS R ON VD.VENDOR_NAME = R.VENDOR_NAME ORDER BY DISTINCT_VENDOR_COUNT DESC, DISTINCT_PRODUCT_COUNT DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':vendorName', $vendorName);
    }
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $json = json_encode(array('items' => $result));
    echo $json;
}


function loadStateData() {
    $vendorName = !empty($vendorName) ? $vendorName : " ";
    $pdo = getConnection();
    $sql = "SELECT STATE, SUM(ANNUAL_SALES) AS total_sales FROM VENDOR_DETAILS GROUP BY STATE";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $json = json_encode(array('items' => $result));
    echo $json;
}
?>