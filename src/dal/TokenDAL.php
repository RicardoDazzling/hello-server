<?php

namespace DazzRick\HelloServer\DAL;

use DazzRick\HelloServer\Entity\Token;
use DazzRick\HelloServer\Exceptions\BadRequestException;
use DazzRick\HelloServer\Exceptions\UnAuthorizedException;
use PH7\JustHttp\StatusCode;
use PH7\PhpHttpResponseHeader\Http;
use RedBeanPHP\R;
use RedBeanPHP\RedException\SQL;

class TokenDAL
{
    public const TABLE_NAME = 'tokens';

    private static function convert(Token|string $token): Token
    {
        if(gettype($token) === 'string') $token = (new Token())->setToken($token);
        return $token;
    }

    public static function validate(?string $token): Token
    {
        if(is_null($token)) throw new UnAuthorizedException();
        if(!self::get($token)) throw new BadRequestException();
        return (new Token())->setToken($token);
    }

    /**
     * @throws SQL
     */
    public static function create(Token|string $token): Token
    {
        $entity = self::convert($token);
        $bean = R::dispense(self::TABLE_NAME);
        $bean->token = $entity->getToken();

        $id = R::store($bean);

        R::close();

        if (gettype($id) === 'integer' || gettype($id) === 'string')return $entity->setId($id);
        else return new Token();
    }

    private static function _find(string $token): NULL|\RedBeanPHP\OODBBean
    {
        $bindings = ['token' => $token];
        return R::findOne(self::TABLE_NAME, 'token = :token ', $bindings);
    }

    public static function get(Token|string $token): bool
    {
        if($token instanceof Token) $token = $token->getToken();
        $bean = self::_find($token);
        return !is_null($bean);
    }


    /**
     * @return Token[]|null[]
     */
    public static function getAll(): array
    {
        $tokens = R::findAll(self::TABLE_NAME);
        if (count($tokens) <= 0) return [];
        else return array_map(function (object $bean): object {
            return (new Token())->setToken($bean->token);
        }, $tokens);
    }

    /**
     * @throws SQL
     */
    public static function remove(Token|string $token): Token
    {
        $entity = self::convert($token);
        $bean = self::_find($entity->getToken());

        if (is_null($bean)) return new Token();

        $works = (bool)R::trash($bean);

        if ($works) return $entity;
        else throw new SQL('Remove error!');
    }

    /**
     * @throws SQL
     */
    public static function update(Token|string $token): Token
    {
        $entity = self::convert($token);
        $bean = self::_find($entity->getToken());

        // If the user exists, update it
        if (is_null($bean)) return new Token();
        $bean->token = $entity->getToken();

        // save the user
        $id = R::store($bean);

        if(gettype($id) === 'integer' || gettype($id) === 'string') return $entity->setId($id);
        else return new Token();
    }
}