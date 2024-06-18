@php
    $urlCurrent = URL::current();
    $children->loadMissing(['slugable', 'activeChildren:id, name, parent_id', 'activeChildren.slugable']);
@endphp

@foreach($children as $category)
    <div class="col-md-4 container_foto shoppy-container child-shoppy-container child-category-{{ $category->parent_id }}" style="padding: 5px;">
        <a href="{{ $category->url }}" class="category-link">
            <article class="text-left">
                <h2>{{ $category->name }}</h2>
            </article>
            <img src="{{ RvMedia::getImageUrl($category->image, null, false, RvMedia::getDefaultImage()) }}" alt="{{ $category->name }}" loading="lazy">
        </a>
    </div>
@endforeach