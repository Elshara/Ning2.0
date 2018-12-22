<?php
/**
 * A mini-box located, for example, on the homepage or a person's profile page.
 */
class XG_Embed {
    private $embedInstanceId;
    private $layoutType;
    private $layoutName;

    public function __construct($embedInstanceId, $layoutType, $layoutName) {
        $this->embedInstanceId = $embedInstanceId;
        $this->layoutType = $layoutType;
        $this->layoutName = $layoutName;
        $this->layout = XG_Layout::load($this->layoutName, $this->layoutType);
    }

    // get a value from $_POST if available and not 'null', else try $_GET; fix for BAZ-8381 [ywh 2008-07-17]
    public static function getValueFromPostGet($key) {
        if (isset($_POST[$key]) && ($_POST[$key] !== 'null')) {
            return $_POST[$key];
        } else {
            return $_GET[$key];
        }
    }

    /** Delimiter for the parts of the "locator" string identifying an embed. */
    const DELIMITER = '/';

    public function getLocator() {
        if (mb_strpos($this->embedInstanceId . $this->layoutType . $this->layoutName, self::DELIMITER) !== false) { throw new Exception('Embed locator contains delimiter character (1521676086)'); }
        return $this->embedInstanceId . self::DELIMITER . $this->layoutType . self::DELIMITER . $this->layoutName;
    }
    public function getType() { return $this->layoutType; }
    public function getName() { return $this->layoutName; }
    public static function load($locator) {
        $values = explode(self::DELIMITER, $locator);
        return new XG_Embed($values[0], $values[1], $values[2]);
    }
    public function isOwnedByCurrentUser() {
        return $this->layout->isOwner();
    }
    public function getOwnerName() {
        return $this->layout->getOwnerName();
    }
    public function get($name) {
        return $this->layout->getEmbedInstanceProperty($name, $this->embedInstanceId);
    }
    public function set($name, $value) {
        // Prevent triggering layout-saving unnecessarily [Jon Aquino 2007-04-27]
        if ('' . $value === $this->get($name)) { return; }
        return $this->layout->setEmbedInstanceProperty($name, $value, $this->embedInstanceId);
    }
}
