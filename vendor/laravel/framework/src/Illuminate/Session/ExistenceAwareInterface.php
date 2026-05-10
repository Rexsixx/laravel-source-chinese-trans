<?php
/**
 * Illuminate，会话，存在意识接口
 */

namespace Illuminate\Session;

interface ExistenceAwareInterface
{
    /**
     * Set the existence state for the session.
	 * 为会话设置存在状态
     *
     * @param  bool  $value
     * @return \SessionHandlerInterface
     */
    public function setExists($value);
}
