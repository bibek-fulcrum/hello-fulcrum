<?php
namespace Aheadworks\SocialLogin\Model\Serialize;

use Magento\Framework\App\ObjectManager;

/**
 * Class Serializer.
 *
 * Wrapper for hiding differences between serialization strategies in 2.1.* and 2.2.* versions magento.
 */
class Serializer
{
    /**
     * Serialize data into string
     *
     * @param string|int|float|bool|array|null $data
     * @return string|bool
     * @throws \InvalidArgumentException
     */
    public function serialize($data)
    {
        if ($serializer = $this->getSerializer()) {
            return $serializer->serialize($data);
        }

        return serialize($data);
    }

    /**
     * Unserialize the given string
     *
     * @param string $string
     * @return string|int|float|bool|array|null
     * @throws \InvalidArgumentException
     */
    public function unserialize($string)
    {
        if ($serializer = $this->getSerializer()) {
            return $serializer->unserialize($string);
        }

        return unserialize($string);
    }

    /**
     * Get serializer.
     *
     * Get serializer if \Magento\Framework\Serialize\SerializerInterface exist.
     * If interface not exist return null.
     *
     * @return mixed
     */
    private function getSerializer()
    {
        $serializerInterfaceName = '\Magento\Framework\Serialize\SerializerInterface';
        if (!interface_exists($serializerInterfaceName)) {
            return null;
        }

        return ObjectManager::getInstance()->get($serializerInterfaceName);
    }
}
