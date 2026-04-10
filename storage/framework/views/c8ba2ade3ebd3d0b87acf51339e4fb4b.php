
<?php $__env->startSection('title', 'Product Comparison'); ?>

<?php $__env->startSection('content'); ?>
<div style="max-width:1100px;margin:0 auto;padding:20px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
        <h2 style="font-size:20px;font-weight:700;color:#1e293b;margin:0"><i class="fas fa-balance-scale" style="color:#3b82f6"></i> Product Comparison</h2>
        <?php if($products->count() > 0): ?>
        <form method="POST" action="<?php echo e(route('compare.clear')); ?>" style="margin:0">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i> Clear all</button>
        </form>
        <?php endif; ?>
    </div>

    <?php if($products->count() === 0): ?>
    <div style="text-align:center;padding:60px 20px;background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.06)">
        <i class="fas fa-balance-scale" style="font-size:48px;color:#cbd5e1;margin-bottom:12px;display:block"></i>
        <p style="font-size:15px;color:#94a3b8;margin-bottom:16px">No products to compare yet</p>
        <a href="<?php echo e(route('home')); ?>" class="btn btn-primary btn-sm"><i class="fas fa-store"></i> Select product</a>
    </div>
    <?php else: ?>
    <div style="overflow-x:auto;-webkit-overflow-scrolling:touch">
        <table style="width:100%;border-collapse:separate;border-spacing:0;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08)">
            
            <thead>
                <tr>
                    <th style="background:#f8fafc;padding:14px 18px;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#64748b;width:180px;border-bottom:2px solid #e2e8f0">Attribute</th>
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <th style="background:#f8fafc;padding:14px 18px;text-align:center;border-bottom:2px solid #e2e8f0;min-width:200px">
                        <div style="position:relative">
                            <button onclick="removeCompare(<?php echo e($p->id); ?>, this)" style="position:absolute;top:-8px;right:-8px;background:#ef4444;color:#fff;border:none;border-radius:50%;width:20px;height:20px;font-size:10px;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1" title="Delete">×</button>
                            <img src="<?php echo e($p->image ?? asset('images/no-image.svg')); ?>" alt="<?php echo e($p->name); ?>" style="width:80px;height:80px;object-fit:contain;border-radius:8px;margin-bottom:8px">
                            <div style="font-size:13px;font-weight:600;color:#1e293b;margin-bottom:4px"><?php echo e($p->name); ?></div>
                            <a href="<?php echo e(route('product.show', $p->id)); ?>" style="font-size:11px;color:#3b82f6;text-decoration:none"><i class="fas fa-eye"></i> View details</a>
                        </div>
                    </th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php for($i = $products->count(); $i < 3; $i++): ?>
                    <th style="background:#fafafa;padding:14px 18px;text-align:center;border-bottom:2px solid #e2e8f0;min-width:200px">
                        <a href="<?php echo e(route('home')); ?>" style="display:inline-flex;flex-direction:column;align-items:center;gap:8px;color:#94a3b8;text-decoration:none;padding:20px">
                            <i class="fas fa-plus-circle" style="font-size:28px;color:#cbd5e1"></i>
                            <span style="font-size:12px">Add product</span>
                        </a>
                    </th>
                    <?php endfor; ?>
                </tr>
            </thead>

            <tbody>
                <?php
                $rows = [
                    ['label'=>'Price',       'field'=>'price',          'type'=>'price'],
                    ['label'=>'Original Price',        'field'=>'original_price', 'type'=>'price'],
                    ['label'=>'Rating',       'field'=>'rating',         'type'=>'rating'],
                    ['label'=>'Reviews',  'field'=>'reviews_count',  'type'=>'number'],
                    ['label'=>'Stock',        'field'=>'stock',          'type'=>'stock'],
                    ['label'=>'Brand',    'field'=>'manufacturer',   'type'=>'text'],
                    ['label'=>'Categories',       'field'=>'category',       'type'=>'relation'],
                ];
                ?>
                <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:12px 18px;font-size:12px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.3px;background:#fafafa;border-right:1px solid #f1f5f9"><?php echo e($row['label']); ?></td>
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <td style="padding:12px 18px;text-align:center;font-size:13px;color:#1e293b">
                        <?php if($row['type'] === 'price'): ?>
                            <?php $val = $p->{$row['field']}; ?>
                            <?php if($val): ?>
                                <strong style="color:<?php echo e($row['field']==='price' ? '#ef4444' : ''); ?>">$<?php echo e(number_format($val, 2)); ?></strong>
                            <?php else: ?>
                                <span style="color:#cbd5e1">—</span>
                            <?php endif; ?>
                        <?php elseif($row['type'] === 'rating'): ?>
                            <?php $r = floatval($p->rating ?? 0); ?>
                            <div style="display:inline-flex;align-items:center;gap:4px">
                                <?php for($i=0;$i<5;$i++): ?>
                                    <i class="fas fa-star" style="font-size:12px;color:<?php echo e($i < $r ? '#f59e0b' : '#e2e8f0'); ?>"></i>
                                <?php endfor; ?>
                                <span style="font-size:12px;margin-left:4px;color:#64748b"><?php echo e($r); ?></span>
                            </div>
                        <?php elseif($row['type'] === 'stock'): ?>
                            <?php $s = $p->stock ?? 0; ?>
                            <span style="color:<?php echo e($s > 0 ? '#10b981' : '#ef4444'); ?>;font-weight:600"><?php echo e($s > 0 ? 'In Stock ('.$s.')' : 'Out of Stock'); ?></span>
                        <?php elseif($row['type'] === 'relation'): ?>
                            <?php echo e($p->category?->name ?? '—'); ?>

                        <?php else: ?>
                            <?php echo e($p->{$row['field']} ?? '—'); ?>

                        <?php endif; ?>
                    </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php for($i = $products->count(); $i < 3; $i++): ?>
                    <td style="padding:12px 18px;text-align:center;color:#cbd5e1;background:#fafafa">—</td>
                    <?php endfor; ?>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:12px 18px;font-size:12px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.3px;background:#fafafa;vertical-align:top;border-right:1px solid #f1f5f9">Description</td>
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <td style="padding:12px 18px;text-align:left;font-size:12px;color:#475569;line-height:1.6"><?php echo e(Str::limit($p->description ?? '—', 120)); ?></td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php for($i = $products->count(); $i < 3; $i++): ?>
                    <td style="background:#fafafa"></td>
                    <?php endfor; ?>
                </tr>

                
                <tr>
                    <td style="padding:14px 18px;background:#f8fafc;border-right:1px solid #f1f5f9"></td>
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <td style="padding:14px 18px;text-align:center;background:#f8fafc">
                        <form action="<?php echo e(route('cart.add', $p->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" style="background:#ef4444;color:#fff;border:none;border-radius:6px;padding:8px 16px;font-size:12px;font-weight:600;cursor:pointer;width:100%">
                                <i class="fas fa-shopping-cart"></i> Add to cart
                            </button>
                        </form>
                    </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php for($i = $products->count(); $i < 3; $i++): ?>
                    <td style="background:#f8fafc"></td>
                    <?php endfor; ?>
                </tr>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<script>
function removeCompare(productId, btn) {
    fetch(`/compare/toggle/${productId}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Content-Type': 'application/json' }
    }).then(() => { window.location.reload(); });
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\ecomerce\resources\views\compare.blade.php ENDPATH**/ ?>