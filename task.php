<?php
    require_once("load.php");
    $patternSKU='#^[0-9]{7}$#';
    $patternUPC='#^[0-9]{1}(\.){0,1}[0-9]{5,10}(E\+1){0,1}$#';
    $patternCategoryName='#^[A-Za-z-,()& ]{2,50}$#';
    if($_GET['task']=='showCategories')
        echo json_encode(getCategories());
    elseif($_GET['task']=='showAllProducts'){
        echo json_encode(getProducts());
    }
    elseif($_GET['task']=='showProductsByCategory'){
        echo json_encode(getProducts($_POST['category']));
    }
    elseif($_GET['task']=='deleteProduct'){
        $idProduct=$_POST['idProduct'];
        $con=mysqli_connect("localhost", "root", "", "task");
        if(mysqli_connect_error())
        {
            echo "Connect to database failed!";
            exit();
        }
        else {
            mysqli_query($con,"DELETE FROM products WHERE idProduct=".$idProduct.";");
            if(!mysqli_error($con))
                echo "Successfully deleted";
            else
                echo "There has been a mistake";
            mysqli_close($con);
        }
    }
    elseif($_GET['task']=='changeProduct'){
        $idProduct=$_POST['idProduct'];
        $regular_price=floatval($_POST['regular_price']);
        $sale_price=floatval($_POST['sale_price']);
        $sku=$_POST['sku'];
        $description=$_POST['description'];
        $idCategory=$_POST['idCategory'];
        $idManufacturer=$_POST['idManufacturer'];
        $upc=$_POST['upc'];
        if(preg_match($patternUPC,$upc) && preg_match($patternSKU,$sku) && is_float($regular_price) && is_float($sale_price)){
            $con=mysqli_connect("localhost", "root", "", "task");
            if(mysqli_connect_error())
            {
                echo "Connect to database failed!";
                exit();
            }
            else {
                mysqli_query($con,"UPDATE products SET sku='".$sku."', upc='".$upc."', regular_price=".$regular_price.", sale_price=".$sale_price.", idCategory=".$idCategory.", idManufacturer=".$idManufacturer.", description='".$description."' WHERE idProduct=".$idProduct.";");
                if(!mysqli_error($con))
                    echo "Successfully changed";
                else
                    echo "There has been a mistake";
                mysqli_close($con);
            }
        }
        else
            echo "Please enter valid data";
    }
    elseif($_GET['task']=='changeCategory'){
        $idCategory=$_POST['idCategory'];
        $nameCategory=$_POST['nameCategory'];
        $idDepartment=$_POST['idDepartment'];
        if(preg_match($patternCategoryName,$nameCategory)){
            $con=mysqli_connect("localhost", "root", "", "task");
            if(mysqli_connect_error())
            {
                echo "Connect to database failed!";
                exit();
            }
            else {
                mysqli_query($con,"UPDATE categories SET idDepartment=".$idDepartment.", nameCategory='".$nameCategory."' WHERE idCategory=".$idCategory.";");
                if(!mysqli_error($con))
                    echo "Successfully changed";
                else
                    echo "There has been a mistake";
                mysqli_close($con);
            }
        }
        else
            echo "Please enter valid category name";
    }
    elseif($_GET['task']=='deleteCategory'){
        $idCategory=$_POST['idCategory'];
        $con=mysqli_connect("localhost", "root", "", "task");
        if(mysqli_connect_error())
        {
            echo "Connect to database failed!";
            exit();
        }
        else {
            mysqli_query($con,"UPDATE PRODUCTS SET idCategory=NULL WHERE idCategory=".$idCategory.";");
            if(!mysqli_error($con)){
                $con1=mysqli_connect("localhost", "root", "", "task");
                if(mysqli_connect_error())
                {
                    echo "Connect to database failed!";
                    exit();
                }
                else{
                    mysqli_query($con,"DELETE FROM categories WHERE idCategory=".$idCategory.";");
                    if(!mysqli_error($con))
                        echo "Successfully deleted";
                    else 
                        echo "There has been a mistake";
                    mysqli_close($con1);
                }
            }
            else
                echo "There has been a mistake";
                mysqli_close($con);
        }
    }
?>