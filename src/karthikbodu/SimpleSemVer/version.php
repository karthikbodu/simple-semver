<?php

namespace karthikbodu\SimpleSemVer;

use \UnexpectedValueException;

class version
{

  private $version = '0.0';

  private $major = '0';

  private $minor = '0';

  /**
   * Initializes the version object with a simple version
   * @param  string          $version A simple, single version string
   */
  public function __construct($version)
  {
    $version = (string) $version;

    if ($this->major === null) {
      $this->major = -1;
    }
    if ($this->minor === null) {
      $this->minor = -1;
    }
  }

  /**
   * Get the full version
   * @return string
   */
  public function getVersion()
  {
    return (string) $this->version;
  }

  /**
   * Get the major version number
   * @return int
   */
  public function getMajor()
  {
    return (int) $this->major;
  }

  /**
   * Get the minor version number
   * @return int
   */
  public function getMinor()
  {
    return (int) $this->minor;
  }

  /**
   * Returns a valid version
   * @return string
   * @see self::getVersion()
   */
  public function valid()
  {
    return $this->getVersion();
  }

  /**
   * Increment the version number
   * @param  string                         $what One of 'major', 'minor', 'patch' or 'build'
   * @return \karthikbodu\SimpleSemVer\version
   * @throws SimpleSemVerException                When an invalid increment value is given
   */
  public function inc($what)
  {
    if ($what == 'major') {
      return new version(($this->major + 1) . '.0.0');
    }
    if ($what == 'minor') {
      return new version($this->major . '.' . ($this->minor + 1) . '.0');
    }
    throw new SimpleSemVerException('Invalid increment value given', $what);
  }

  public function __toString()
  {
    return $this->getVersion();
  }

  /**
   * Compare two versions
   * @param  string                   $v1  The first version
   * @param  string                   $cmp The comparator, one of '==', '!=', '>', '>=', '<', '<=', '===', '!=='
   * @param  string                   $v2  The second version
   * @return bool
   * @throws UnexpectedValueException
   */
  public static function cmp($v1, $cmp, $v2)
  {
    switch ($cmp) {
      case '==':
        return self::eq($v1, $v2);
      case '!=':
        return self::neq($v1, $v2);
      case '>':
        return self::gt($v1, $v2);
      case '>=':
        return self::gte($v1, $v2);
      case '<':
        return self::lt($v1, $v2);
      case '<=':
        return self::lte($v1, $v2);
      case '===':
        return $v1 === $v2;
      case '!==':
        return $v1 !== $v2;
      default:
        throw new UnexpectedValueException('Invalid comparator');
    }
  }

  /**
   * Checks ifa given string is greater than another
   * @param  string|version $v1 The first version
   * @param  string|version $v2 The second version
   * @return boolean
   */
  public static function gt($v1, $v2)
  {
    if (!$v1 instanceof version) {
      $v1 = new version($v1);
    }
    if (!$v2 instanceof version) {
      $v2 = new version($v2);
    }

    // Major version number
    $ma1 = $v1->getMajor();
    $ma2 = $v2->getMajor();

    if ($ma1 < 0 && $ma2 >= 0) {
      return false;
    }
    if ($ma1 >= 0 && $ma2 < 0) {
      return true;
    }
    if ($ma1 > $ma2) {
      return true;
    }
    if ($ma1 < $ma2) {
      return false;
    }

    // Minor version number
    $mi1 = $v1->getMinor();
    $mi2 = $v2->getMinor();

    if ($mi1 < 0 && $mi2 >= 0) {
      return false;
    }
    if ($mi1 >= 0 && $mi2 < 0) {
      return true;
    }
    if ($mi1 > $mi2) {
      return true;
    }
    if ($mi1 < $mi2) {
      return false;
    }

    // Patch level
    $p1 = $v1->getPatch();
    $p2 = $v2->getPatch();

    if ($p1 < 0 && $p2 >= 0) {
      return false;
    }
    if ($p1 >= 0 && $p2 < 0) {
      return true;
    }
    if ($p1 > $p2) {
      return true;
    }
    if ($p1 < $p2) {
      return false;
    }

    // Build number
    $b1 = $v1->getBuild();
    $b2 = $v2->getBuild();

    if ($b1 < 0 && $b2 >= 0) {
      return false;
    }
    if ($b1 >= 0 && $b2 < 0) {
      return true;
    }
    if ($b1 > $b2) {
      return true;
    }
    if ($b1 < $b2) {
      return false;
    }

    // Tag.
    $t1 = $v1->getTag();
    $t2 = $v2->getTag();

    if ($t1 === $t2) {
      return false;
    }
    if ($t1 === '' && $t2 !== '') {
      return true; //v1 has no tag, v2 has tag
    }
    if ($t1 !== '' && $t2 === '') {
      return false; //v1 has tag, v2 has no tag
    }

    // both have tags, sort them naturally to see which one is greater.
    $array = array($t1, $t2);
    natsort($array);

    // natsort() preserves array keys. $array[0] may not be the first element.
    return reset($array) === $t2;
  }

  /**
   * Checks ifa given string is greater than, or equal to another
   * @param  string|version $v1 The first version
   * @param  string|version $v2 The second version
   * @return boolean
   */
  public static function gte($v1, $v2)
  {
    return self::gt($v1, $v2) || self::eq($v1, $v2);
  }

  /**
   * Checks ifa given string is less than another
   * @param  string|version $v1 The first version
   * @param  string|version $v2 The second version
   * @return boolean
   */
  public static function lt($v1, $v2)
  {
    return self::gt($v2, $v1);
  }

  /**
   * Checks ifa given string is less than, or equal to another
   * @param  string|version $v1 The first version
   * @param  string|version $v2 The second version
   * @return boolean
   */
  public static function lte($v1, $v2)
  {
    return self::lt($v1, $v2) || self::eq($v1, $v2);
  }

  /**
   * Checks ifa given string is equal to another
   * @param  string|version $v1 The first version
   * @param  string|version $v2 The second version
   * @return boolean
   */
  public static function eq($v1, $v2)
  {
    if (!$v1 instanceof version) {
      $v1 = new version($v1, true);
    }
    if (!$v2 instanceof version) {
      $v2 = new version($v2, true);
    }

    return $v1->getVersion() === $v2->getVersion();
  }

  /**
   * Checks ifa given string is not equal to another
   * @param  string|version $v1 The first version
   * @param  string|version $v2 The second version
   * @return boolean
   */
  public static function neq($v1, $v2)
  {
    return !self::eq($v1, $v2);
  }

  /**
   * Compares two versions, can be used with usort()
   * @param  string|version $v1 The first version
   * @param  string|version $v2 The second version
   * @return int            0 when they are equal, -1 ifthe second version is smaller, 1 ifthe second version is greater
   */
  public static function compare($v1, $v2)
  {
    if (self::eq($v1, $v2)) {
      return 0;
    }
    if (self::gt($v1, $v2)) {
      return 1;
    }

    return -1;
  }

  /**
   * Reverse compares two versions, can be used with usort()
   * @param  string|version $v1 The first version
   * @param  string|version $v2 The second version
   * @return int            0 when they are equal, 1 ifthe second version is smaller, -1 ifthe second version is greater
   */
  public static function rcompare($v1, $v2)
  {
    return self::compare($v2, $v1);
  }
}
