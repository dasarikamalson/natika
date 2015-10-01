{{-- Part of phoenix project. --}}
<?php
use Natika\Script\EditorScript;
use Phoenix\Script\PhoenixScript;

PhoenixScript::core('#admin-form');
EditorScript::highlight('.topic-post pre');
?>

@extends('_global.html')

@section('page_title')
Topic
@stop

@section('banner_inner')

    <h1>
        <span class="fa fa-bullhorn"></span>
        {{ \Phoenix\Html\Document::getTitle() }}
        @if (\Natika\User\UserHelper::canEditPost($posts[0]))
            <small><a href="" data-toggle="modal" data-target="#topicUpdateModal"><span class="fa fa-edit"></span></a></small>
        @endif
    </h1>

    <ol class="breadcrumb">
        @foreach ($breadcrumbs as $breadcrumb)
            <li>
                <a href="{{ $breadcrumb->link }}">
                    {{ $breadcrumb->title }}
                </a>
            </li>
        @endforeach
    </ol>

    {{-- Title Update --}}

    @include('update')
@stop

@section('content')

<div class="container topic-item">

    <form action="{{ $router->html('post') }}" id="admin-form" method="post">
        <input name="_method" type="hidden" value="" />
        {!! \Windwalker\Core\Security\CsrfProtection::input() !!}
    </form>

    @foreach ($posts as $post)
        <div class="topic-post-wrap left-line">
            <article class="topic-post row clearfix">

                <div class="col-md-1">
                    <img class="post-img circle-icon icon-large" src="{{ $post->user_avatar }}">
                </div>

                <div class="col-md-11">
                    <div class="panel {{ $post->primary ? 'panel-primary' : 'panel-default' }}">
                        <div class="panel-heading clearfix">
                            <div class="pull-left">
                                {{ $post->user_name }} &nbsp;<small class="{{ $post->primary ? '' : 'text-muted' }}">{{ '@' . $post->user_username }}</small>
                            </div>
                            <div class="pull-right">
                                <span id="reply-{{ $post->id }}" class="fixed-anchor"></span>
                                <a href="#reply-{{ $post->id }}" class="text-muted">
                                    {{ $helper->date->since($post->created) }}
                                </a>

                                @if (\Natika\User\UserHelper::canEditOwnPost($post))
                                    &nbsp;
                                    <a href="{{ $router->html('post_edit', array('id' => $post->id)) }}" class="text-muted">
                                        <span class="fa fa-edit"></span>
                                    </a>
                                @endif

                                @if (\Natika\User\UserHelper::canDeletePost($post))
                                    &nbsp;
                                    <a href="javascript: void(0);" class="text-muted" onclick="if (confirm('Are you sure')) Phoenix.sendDelete(null, {cid: [{{ $post->id }}]});">
                                        <span class="fa fa-trash"></span>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="panel-body markdown-body">
                            {!! $post->body !!}
                        </div>
                    </div>
                </div>

            </article>
        </div>
    @endforeach

    <div class="left-line">
        <div class="pagination-wrap text-right row">
            <div class="col-md-12">
                {!! $pagination->render(function ($queries) use ($router, $topic)
        {
            $queries['path'] = $topic->category->path;
            $queries['id'] = $topic->id;

            return $router->html('topic', $queries);
        }, 'windwalker.pagination.phoenix') !!}
            </div>
        </div>
    </div>


    <div class="reply-block row clearfix">

        @if (\Natika\User\UserHelper::isLogin())
        <div class="col-md-1">
            <img class="post-img circle-icon icon-large pull-left" src="{{ $user->avatar }}">
        </div>

        <div class="col-md-11">
            <form action="{{ $router->html('post') }}" method="post" id="post-form">
                @include('_global.natika.editor', array('post' => new \Windwalker\Data\Data, 'reply_button' => true, 'title_field' => false))

                <div class="hidden-inputs">
                    {!! \Windwalker\Core\Security\CsrfProtection::input() !!}
                    <input type="hidden" name="item[topic_id]" value="{{ $topic->id }}" />
                </div>
            </form>
        </div>
        @else
        <a class="btn btn-default btn-lg" href="{{ $router->html('login', array('return' => base64_encode($uri['full'] . '#editor'))) }}">
            <span class="fa fa-github"></span>
            Login to reply
        </a>
        @endif
    </div>

</div>
@stop
