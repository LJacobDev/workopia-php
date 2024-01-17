<?php


/**
 * Get the base path
 * 
 * @param string $path
 * @return string
 */

 function basePath($path = ''){
    return __DIR__ . '/' . $path;
 }


 /**
  * Load a view
  *
  * @param string $name
  * @return void
  */
  function loadView($name){
    
    $viewPath = basePath("Views/{$name}.view.php");

    if(file_exists($viewPath)){
        require $viewPath;
    } else {
        echo "View \"{$name}\" not found";
    }
  }
 
  /**
  * Load a partial
  *
  * @param string $name
  * @return void
  */
  function loadPartial($name){
   
    $partialPath = basePath("Views/partials/{$name}.php");

    if(file_exists($partialPath)) {
        require $partialPath;
    } else {
        echo "Partial \"{$name}\" not found";
    }
  }



  /**
   * Inspect a value(s)
   * 
   * @param mixed $value
   * @return void
   */
  function inspect($value){
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
  }

    /**
   * Inspect a value(s) and then stop script
   * 
   * @param mixed $value
   * @return void
   */
  function inspectAndDie($value){
    inspect($value);
    die();
  }