<?php

namespace SSone\CMSBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;


class UserRepository extends EntityRepository implements UserProviderInterface
{
    public function loadUserByUsername($username)
    {
        $q = $this
            ->createQueryBuilder('u')
            ->select('u, r')
            ->leftJoin('u.roles', 'r')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery();

        try {
            // The Query::getSingleResult() method throws an exception
            // if there is no record matching the criteria.
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            $message = sprintf(
                'Unable to find an active admin AcmeCMSBundle:User object identified by "%s".',
                $username
            );
            throw new UsernameNotFoundException($message, 0, $e);
        }

        return $user;
    }

    public function findBySecurekey($securekey)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT c FROM SSoneCMSBundle:User c WHERE c.securekey = :securekey'
            )->setParameter('securekey', $securekey)
            ->getSingleResult();
    }


    public function getItemsForListTable()
    {
        $data['type'] = "Field Types";
        $data['columns'] = array(
            "Username"=>"username",
            "E-mail"=>"email",
            "Last Modified"=>"modifiedAt",
            "Last Modified By"=>"modifiedBy",
        );
        $data['items'] = $this->getEntityManager()
            ->createQuery(
                'SELECT
                c.id,
                c.securekey,
                c.username,
                c.email,
                c.modifiedAt,
                c.modifiedBy
                FROM SSoneCMSBundle:User c
                ORDER BY c.username ASC'
            )
            ->getResult();


        return $data;
    }

    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf(
                    'Instances of "%s" are not supported.',
                    $class
                )
            );
        }

        return $this->find($user->getId());
    }

    public function supportsClass($class)
    {
        return $this->getEntityName() === $class
        || is_subclass_of($class, $this->getEntityName());
    }
}
