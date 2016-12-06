<div id="mainbar-full">
 <div class="subheader">
  <h1 id="h-tags">Tags</h1>
  <div id="tabs">
   <a href="?tab=popular" title="most popular tags" data-value="popular">popular</a>
   <a href="?tab=name" title="tags in alphabetical order" data-value="name">name</a>
   <a href="?tab=new" title="recently created tags" data-value="new">new</a>
  </div>
 </div>
 
 <div class="page-description">
  <p>
   A tag is a keyword or label that categorizes your question with other, similar questions. 
   Using the right tags makes it easier for others to find and answer your question.
  </p>
 </div>
 <?php PageModel::add_template_fragment('tags-browse-list', array('rows'=>15, 'cols'=>4)); ?>
</div>