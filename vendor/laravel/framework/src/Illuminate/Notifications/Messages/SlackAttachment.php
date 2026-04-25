<?php
/**
 * Illuminate，通知，信息，Slack 的附件
 */

namespace Illuminate\Notifications\Messages;

use Illuminate\Support\InteractsWithTime;

class SlackAttachment
{
    use InteractsWithTime;

    /**
     * The attachment's title.
	 * 附件的标题
     *
     * @var string
     */
    public $title;

    /**
     * The attachment's URL.
	 * 附件的URL
     *
     * @var string
     */
    public $url;

    /**
     * The attachment's text content.
	 * 附件的文本内容
     *
     * @var string
     */
    public $content;

    /**
     * A plain-text summary of the attachment.
	 * 附件的纯文本摘要
     *
     * @var string
     */
    public $fallback;

    /**
     * The attachment's color.
	 * 附件的颜色
     *
     * @var string
     */
    public $color;

    /**
     * The attachment's fields.
	 * 附件的字段
     *
     * @var array
     */
    public $fields;

    /**
     * The fields containing markdown.
	 * 包含降价的字段
     *
     * @var array
     */
    public $markdown;

    /**
     * The attachment's image url.
	 * 附件的图像url
     *
     * @var string
     */
    public $imageUrl;

    /**
     * The attachment's thumb url.
	 * 附件的thum burl
     *
     * @var string
     */
    public $thumbUrl;

    /**
     * The attachment author's name.
	 * 附件作者姓名
     *
     * @var string
     */
    public $authorName;

    /**
     * The attachment author's link.
	 * 附件作者的链接
     *
     * @var string
     */
    public $authorLink;

    /**
     * The attachment author's icon.
	 * 附件作者的图标
     *
     * @var string
     */
    public $authorIcon;

    /**
     * The attachment's footer.
	 * 附件的页脚
     *
     * @var string
     */
    public $footer;

    /**
     * The attachment's footer icon.
	 * 附件的页脚图标
     *
     * @var string
     */
    public $footerIcon;

    /**
     * The attachment's timestamp.
	 * 附件的时间戳
     *
     * @var int
     */
    public $timestamp;

    /**
     * Set the title of the attachment.
	 * 设置附件的标题
     *
     * @param  string  $title
     * @param  string|null  $url
     * @return $this
     */
    public function title($title, $url = null)
    {
        $this->title = $title;
        $this->url = $url;

        return $this;
    }

    /**
     * Set the content (text) of the attachment.
	 * 设置附件的内容（文本）
     *
     * @param  string  $content
     * @return $this
     */
    public function content($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * A plain-text summary of the attachment.
	 * 附件的纯文本摘要
     *
     * @param  string  $fallback
     * @return $this
     */
    public function fallback($fallback)
    {
        $this->fallback = $fallback;

        return $this;
    }

    /**
     * Set the color of the attachment.
	 * 设置附件的颜色
     *
     * @param  string  $color
     * @return $this
     */
    public function color($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Add a field to the attachment.
	 * 向附件添加一个字段
     *
     * @param  \Closure|string $title
     * @param  string $content
     * @return $this
     */
    public function field($title, $content = '')
    {
        if (is_callable($title)) {
            $callback = $title;

            $callback($attachmentField = new SlackAttachmentField);

            $this->fields[] = $attachmentField;

            return $this;
        }

        $this->fields[$title] = $content;

        return $this;
    }

    /**
     * Set the fields of the attachment.
	 * 设置附件的字段
     *
     * @param  array  $fields
     * @return $this
     */
    public function fields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Set the fields containing markdown.
	 * 设置包含降价的字段
     *
     * @param  array  $fields
     * @return $this
     */
    public function markdown(array $fields)
    {
        $this->markdown = $fields;

        return $this;
    }

    /**
     * Set the image URL.
	 * 设置图像URL
     *
     * @param  string  $url
     * @return $this
     */
    public function image($url)
    {
        $this->imageUrl = $url;

        return $this;
    }

    /**
     * Set the URL to the attachment thumbnail.
	 * 将URL设置为附件缩略图
     *
     * @param  string  $url
     * @return $this
     */
    public function thumb($url)
    {
        $this->thumbUrl = $url;

        return $this;
    }

    /**
     * Set the author of the attachment.
	 * 设置附件的作者
     *
     * @param  string  $name
     * @param  string|null  $link
     * @param  string|null  $icon
     * @return $this
     */
    public function author($name, $link = null, $icon = null)
    {
        $this->authorName = $name;
        $this->authorLink = $link;
        $this->authorIcon = $icon;

        return $this;
    }

    /**
     * Set the footer content.
	 * 设置页脚内容
     *
     * @param  string  $footer
     * @return $this
     */
    public function footer($footer)
    {
        $this->footer = $footer;

        return $this;
    }

    /**
     * Set the footer icon.
	 * 设置页脚图标
     *
     * @param  string $icon
     * @return $this
     */
    public function footerIcon($icon)
    {
        $this->footerIcon = $icon;

        return $this;
    }

    /**
     * Set the timestamp.
	 * 设置时间戳
     *
     * @param  \DateTimeInterface|\DateInterval|int  $timestamp
     * @return $this
     */
    public function timestamp($timestamp)
    {
        $this->timestamp = $this->availableAt($timestamp);

        return $this;
    }
}
