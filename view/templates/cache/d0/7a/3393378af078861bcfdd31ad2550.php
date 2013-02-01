<?php

/* common/container.html.twig */
class __TwigTemplate_d07a3393378af078861bcfdd31ad2550 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'head' => array($this, 'block_head'),
            'body' => array($this, 'block_body'),
            'header' => array($this, 'block_header'),
            'content' => array($this, 'block_content'),
            'footer' => array($this, 'block_footer'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"en\">
    <head>
        ";
        // line 4
        $this->displayBlock('head', $context, $blocks);
        // line 11
        echo "    </head>
    <body>
        ";
        // line 13
        $this->displayBlock('body', $context, $blocks);
        // line 32
        echo "    </body>
</html>";
    }

    // line 4
    public function block_head($context, array $blocks = array())
    {
        // line 5
        echo "            <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
            
            ";
        // line 7
        if (isset($context["container_includes"])) { $_container_includes_ = $context["container_includes"]; } else { $_container_includes_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_container_includes_);
        foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
            // line 8
            echo "                test ";
            if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
            echo twig_escape_filter($this->env, $_i_, "html", null, true);
            echo " 
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 10
        echo "        ";
    }

    // line 13
    public function block_body($context, array $blocks = array())
    {
        // line 14
        echo "            <div class='container'>
                <header id='header'>
                    ";
        // line 16
        $this->displayBlock('header', $context, $blocks);
        // line 19
        echo "                </header>
                <div id='content'>
                    ";
        // line 21
        $this->displayBlock('content', $context, $blocks);
        // line 24
        echo "                </div>
                <footer id='footer' class='footer'>
                    ";
        // line 26
        $this->displayBlock('footer', $context, $blocks);
        // line 29
        echo "                </footer>
            </div>
        ";
    }

    // line 16
    public function block_header($context, array $blocks = array())
    {
        // line 17
        echo "                        <h1>Twig test</h1>
                    ";
    }

    // line 21
    public function block_content($context, array $blocks = array())
    {
        // line 22
        echo "                        
                    ";
    }

    // line 26
    public function block_footer($context, array $blocks = array())
    {
        // line 27
        echo "                        HT2 ";
        if (isset($context["footer_year"])) { $_footer_year_ = $context["footer_year"]; } else { $_footer_year_ = null; }
        echo twig_escape_filter($this->env, $_footer_year_, "html", null, true);
        echo "
                    ";
    }

    public function getTemplateName()
    {
        return "common/container.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  114 => 27,  111 => 26,  106 => 22,  103 => 21,  98 => 17,  95 => 16,  89 => 29,  87 => 26,  83 => 24,  81 => 21,  77 => 19,  75 => 16,  71 => 14,  68 => 13,  64 => 10,  54 => 8,  49 => 7,  45 => 5,  42 => 4,  35 => 13,  31 => 11,  24 => 1,  40 => 8,  37 => 32,  32 => 4,  29 => 4,);
    }
}
