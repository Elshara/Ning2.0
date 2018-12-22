<?php

/**
 * This class assists with shape-manipulation activity, which is primarily used
 * for defining how different types of content objects behave with regard to
 * search.
 *
 * @ingroup XG
 */
class XG_ShapeHelper {

    /**
     * Set the indexing property for one or more attributes.
     *
     * @param $type string The type (shape name) to modify
     * @param $indexing array An array of attributes to modify. Keys of this
     *   array should be attribute names (e.g. "title" or "my.flavor". values
     *   of this array should be valid indexing values (e.g. 'ignored', 
     *   'stored','phrase','text','fulltext'
     */
    public static function addIndexingForType($type, $indexing) {
        $shape = XN_Shape::load($type);
        if (is_null($shape)) {
            throw new XN_IllegalArgumentException("No shape for type: $type");
        }
        $shape = self::addIndexingProper($shape, $indexing);
        $shape->save();
    }
    
   /**
    * Reset the indexing for a shape to only the specified attributes and
    * indexing values.
    *
    * @param $type string The type (shape name) to modify
    * @param $indexing array An array of attributes to modify. Keys of this
    *   array should be attribute names (e.g. "title" or "my.flavor". values
    *   of this array should be valid indexing values (e.g. 'ignored', 
    *   'stored','phrase','text','fulltext'
    */
   public static function setIndexingForType($type, $indexing) {
        $shape = XN_Shape::load($type);
        if (is_null($shape)) {
            throw new XN_IllegalArgumentException("No shape for type: $type");
        }
        $shape = self::clearIndexingProper($shape);
        $shape = self::addIndexingProper($shape, $indexing);
        $shape->save();
   }
    
   /**
    * Clear searchability status from all attributes in a content object
    * and mark the content object as not searchable
    *
    * @param $type string The type (shape name) to modify
    */
   public static function clearIndexingForType($type) {
       $shape = XN_Shape::load($type);
       if (is_null($shape)) {
           throw new XN_IllegalArgumentException("No shape for type: $type");
       }
       $shape = self::clearIndexingProper($shape);
       $shape->save();
   }

   /**
    * Helper to set an already-loaded shape as searchable and set the
    * indexing property on some attributes
    *
    * @param $shape XN_Shape The shape to modify
    * @param $indexing array An array of attributes to modify. Keys of this
    *   array should be attribute names (e.g. "title" or "my.flavor". values
    *   of this array should be valid indexing values (e.g. 'ignored', 
    *   'stored','phrase','text','fulltext'
    * @return XN_Shape The modified shape
    * 
    */
   protected static function addIndexingProper($shape, $indexing) {
       if (count($indexing)) {
           $shape->searchable = true;
       }
       foreach ($indexing as $attributeName => $indexingValue) {
           $setArgs = array('indexing' => $indexingValue);
           /* If the attribute isn't in the shape yet, set it to be a string */
           if (! isset($shape->attributes[$attributeName])) {
               $setArgs['type'] = XN_Attribute::STRING;
           }
           $shape->setAttribute($attributeName, $setArgs);
       }
       return $shape;
   }
   
   /**
    * Helper to clear out indexing on an already-loaded shape without 
    * loading or saving
    *
    * @param $shape XN_Shape The shape to modify
    * @return XN_Shape The modified shape
    */
   protected static function clearIndexingProper($shape) {
       $shape->searchable = false;
       foreach ($shape->attributes as $attributeName => $attribute) {
           $shape->setAttribute($attributeName,array('indexing' => 'ignored'));
       }
       return $shape;
   }
   
   /**
    * Return an array representing the indexing status of the attributes
    * in the given model
    *
    * @param $model string The model class name to introspect
    */
   public static function indexingForModel($model) {
       /* If there's a searchConfiguration method in the model class,
        * then just call that to get what we need. Otherwise, use the
        * values from the "@feature indexing" annotations */
       $callback = array($model, 'searchConfiguration');
       if (is_callable($callback)) {
           $indexing = call_user_func($callback);
       }
       else {
           $indexing = self::indexingFromAnnotationForModel($model);
       }
       return $indexing;
   }
   
   /**
    * Return an array representing the indexing status of attributes in the model
    * that are marked indexable with the "@feature indexing" annotation
    *
    * @param $model string The model class name to introspect
    * @return array The attribute name => indexing status map
    */
   public static function indexingFromAnnotationForModel($model) {
       $indexing = array();
       try {
           $wShape = W_Cache::getShape($model);
           foreach ($wShape->attrs as $a) {
               if (count($a->features)) {
                   foreach ($a->features as $feature) {
                       if ($feature->name == 'indexing') {
                            $attrName = isset(W_Content::$_systemAttributes[$a->name]) ? $a->name : ('my.' . $a->name);
                            $indexing[$attrName] = $feature->arg;
                       }
                   }
               }
           }
       } catch (Exception $e) {
           /* If the shape can't be retrieved, then there's no indexing to do */
       }
       return $indexing;
   }
   
   /**
    * All models should generally have these fields indexed
    *
    * @return array The attribute name => indexing status map
    */
    public static function defaultIndexing() {
        return array(/*'type' => 'text',*/ 'author' => 'text');
    }
    
   /**
    * Update the XN_Shape for a model with appropriate indexing values, 
    * based on the standard conventions of "@feature indexing" annotations
    * and/or the optional searchConfiguration() method in the models
    *
    * @param $model string The model to set indexing for
    */
    public static function setStandardIndexingForModel($model) {
        $indexing = self::indexingForModel($model);
        self::setIndexingForType($model, $indexing);        
    }
    
   /**
    * Update the XN_Shape for all models the network knows about with
    * appropriate indexing values based on the standard conventions.
    * @see XG_ShapeHelper::setStandardIndexingForModel()
    */
    public static function setStandardIndexingForAllModels() {
        $modelsByWidget = self::getModelsByWidget();
        foreach ($modelsByWidget as $widgetName => $widgetModels) {
            $shouldSave = false;
            foreach ($widgetModels as $model) {
                try {
                    if (self::isModelIndexingComplete($model) === false) {
                        $changed = self::markModelIndexingComplete($model, false);
                    }
                    if ($changed) { $shouldSave = true; }
                } catch (XN_IllegalArgumentException $e) {
                    /* The shape doesn't exist yet, it'll get updated on first save */
                }
            }
            if ($shouldSave) {
                W_Cache::getWidget($widgetName)->saveConfig();
            }
        }
    }
    
   /**
    * Return whether indexing has been set up for a particular model.
    *
    * @param $model string The model to check
    * @return null|boolean null: can't set up indexing for this model
    *                      true: yes, indexing setup is complete
    *                      false: no, indexing setup is not yet complete
    */
   public static function isModelIndexingComplete($model) {
       try {
             $widget = W_Cache::getModel($model);
         } catch (Exception $e) {
             /* If this is just some random content type not associated with
              * model then don't bother adjusting indexing */
             return null;
         }
         
         /* If the indexing of this type has already been dealt with, then
          * don't do anything else */
         if (mb_strlen($widget->config["indexing_$model"]) != 0) {
             return true;
         }
         
         return false;
   }

   /**
    * Mark that a particular model has had indexing set up.
    *
    * @param $model string The model to check
    * @param $saveConfig boolean Save the config after changing it?
    * @return boolean true if the config var was set to "set"
    */
   public static function markModelIndexingComplete($model, $saveConfig = true) {
       try {
             $widget = W_Cache::getModel($model);
       } catch (Exception $e) {
             /* If this is just some random content type not associated with
              * model then don't bother adjusting indexing */
              return false;
       }
       try {
           self::setStandardIndexingForModel($model);
           $widget->config["indexing_$model"] = "set";
           if ($saveConfig) {
               $widget->saveConfig();
           }
       } catch (Exception $e) {
           error_log("Couldn't set indexing for $model: " . $e->getMessage());
           return false;
       }
       
       return true;
   }
   
  /**
   * Update the shape of models whose model definitions (PHP files)
   * indicate that they should be searchable, but whose shapes (via
   * the shape API) are not marked as searchable. This method updates
   * models whether or not the config var for the model is set.
   *
   * @return array names of the models that were reset
   */
  public static function setStandardIndexingForSearchableModels() {
      $modelsByWidget = self::getModelsByWidget();
      $resetModels = array();
      foreach ($modelsByWidget as $widgetName => $widgetModels) {
          $widget = W_Cache::getWidget($widgetName);
          $shouldSave = false;
          foreach ($widgetModels as $model) {
              if (count($indexing = self::indexingForModel($model))) {
                  $shape = XN_Shape::load($model);
                  if (! $shape->searchable) {
                      try {
                          self::setIndexingForType($model, $indexing);
                          $resetModels[] = $model;
                          if (! mb_strlen($widget->config["indexing_$model"])) {
                              $widget->config["indexing_$model"] = 'set';
                              $shouldSave = true;
                          }
                      } catch (XN_IllegalArgumentException $e) {
                          /* The shape doesn't exist yet */
                      }
                  }
              }
          }
          if ($shouldSave) {
              $widget->saveConfig();
          }
      }
      return $resetModels;
  }
  
  /**
   * Prepare a list of all the models in the app, organized by
   * which widget owns them.
   *
   * @return array
   */
  protected function getModelsByWidget() {
      $models = W_Cache::allModels();
      $modelsByWidget = array();
      foreach ($models as $model => $widget) {
          $modelsByWidget[$widget->dir][] = $model;
      }
      return $modelsByWidget;
  }
}
