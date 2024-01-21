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
  function loadView($name, $data = []){
    
    $viewPath = basePath("App/Views/{$name}.view.php");

    if(file_exists($viewPath)){
      
      //this will take any variables in the array and make them available here
      extract($data); //confirmed that commenting out this line results in 'undefined' error

      //now the file at viewPath will have these extracted variables available to it
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
   
    $partialPath = basePath("App/Views/partials/{$name}.php");

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


  /**
   * Format salary
   * 
   * @param string $salary
   * @return string Formatted Salary
   */
  function formatSalary($salary) {
    return '$' . number_format((float)$salary);
  }



  /**
   * Sanitize Data
   * 
   * @param string $dirty
   * @return string
   */
  function sanitize($dirty) {
    return filter_var(trim($dirty), FILTER_SANITIZE_SPECIAL_CHARS);
  }