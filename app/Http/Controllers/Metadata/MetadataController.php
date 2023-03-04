<?php

namespace App\Http\Controllers\Metadata;

use App\Http\Controllers\Controller;
use App\Models\Metadata;
class MetadataController extends Controller
{
    /**
     * @param $name
     * @param $value
     * @param $quiet
     * @return void
     *
     * Set a data of a specific entry. If entry is not set in database, create first an instance.
     * If an event should not be fired, the quiet option can be used.
     */
    static public function setMetadata($name, $value, $quiet = false)
    {
        $metadata = Metadata::where("name", $name)->first();
        //Check if Metadata exists already
        if (is_null($metadata)) {
            //if not, create an instance
            $metadata = new Metadata();
            $metadata->name = $name;
        }

        $metadata->value = $value;
        if ($quiet) {
            // Don't fire an update or create event
            $metadata->saveQuietly();
        } else {
            $metadata->save();
        }

    }

    /**
     * @param $name
     * @param $default
     * @return mixed|null
     *
     * Gat a data of a specific entry. If entry is not set in database, return the default value.
     */
    static public function getMetadata($name, $default = null)
    {
        $ret = $default;

        $metadata = Metadata::where("name", $name)->first();
        // if metadata not exists, return default value "null" or passed param
        if (!is_null($metadata)) {
            $ret = $metadata->value;
        }

        return $ret;
    }
}
