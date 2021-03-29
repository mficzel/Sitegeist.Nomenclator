=================
Utility Functions
=================

This chapter contains short introductions to helpful utility functions available
in Flow. Please see the API documentation for a full reference:

* ``Neos\Utility\ObjectAccess`` should be used to get/set properties on
  objects, arrays and similar structures.

* ``Neos\Utility\Arrays`` contains some array helper functions for merging
  arrays or creating them from strings.

* ``Neos\Utility\Files`` contains functions for manipulating files and directories,
  and for unifying file access across the different platforms.

* ``Neos\Utility\MediaTypes`` contains a list of internet media types and
  their corresponding file types, and can be used to map between them.

* ``Neos\Flow\Utility\Now`` is a singleton ``DateTime`` class containing
  the current time. It should always be used when you need access to the current
  time.
