<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;

trait EncryptableTrait
{
    /**
     * Encrypt the given value.
     *
     * @param  mixed  $value
     * @return string
     */
    public function encrypt($value)
    {
        return Crypt::encrypt($value);
    }

    /**
     * Decrypt the given value.
     *
     * @param  string  $value
     * @return mixed
     */
    public function decrypt($value)
    {
        return Crypt::decrypt($value);
    }

    /**
     * Encrypt an attribute before storing it in the database.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->encryptable)) {
            $value = $this->encrypt($value);
        }

        parent::setAttribute($key, $value);
    }

    /**
     * Decrypt an attribute after retrieving it from the database.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (in_array($key, $this->encryptable)) {
            $value = $this->decrypt($value);
        }

        return $value;
    }
}
// add to the model you want to encrypt
//   protected $encryptable = [
//         'email',
//     ];