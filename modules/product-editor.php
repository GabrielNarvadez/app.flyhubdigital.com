<div class="container-fluid my-4">
  <form id="product-form">
    <div class="row">
      <!-- Main Column -->
      <div class="col-lg-8 mb-4">
        <div class="card mb-4">
          <div class="card-body">
            <h4 class="mb-3">Product Details</h4>
            
            <div class="mb-3">
              <label for="product-name" class="form-label">Product Name</label>
              <input type="text" class="form-control" id="product-name" name="product_name" required>
            </div>
            
            <div class="mb-3">
              <label for="product-description" class="form-label">Description</label>
              <!-- Replace with your preferred WYSIWYG editor -->
              <textarea class="form-control" id="product-description" name="product_description" rows="5"></textarea>

              <script>
                tinymce.init({
                  selector: '#product-description',
                  height: 250,
                  menubar: false,
                  plugins: 'lists link image preview',
                  toolbar: 'undo redo | formatselect | bold italic underline | bullist numlist | link image | preview',
                  branding: false
                });
              </script>


            </div>
            
            <div class="row g-3">
              <div class="col-md-6">
                <label for="product-type" class="form-label">Product Type</label>
                <select class="form-select" id="product-type" name="product_type">
                  <option value="goods">Goods</option>
                  <option value="service">Service</option>
                  <option value="combo">Combo</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="product-price" class="form-label">Price</label>
                <input type="number" class="form-control" id="product-price" name="product_price" step="0.01" min="0">
              </div>
            </div>
            
            <div class="mb-3 mt-3">
              <label class="form-label">Upload Media</label>
              <input class="form-control" type="file" id="product-media" name="product_media[]" multiple>
            </div>
          </div>
        </div>

        <!-- Inventory Details -->
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="mb-3">Inventory</h5>
            <div class="row g-3">
              <div class="col-md-4">
                <label for="sku" class="form-label">SKU</label>
                <input type="text" class="form-control" id="sku" name="sku">
              </div>
              <div class="col-md-4">
                <label for="barcode" class="form-label">Barcode</label>
                <div class="input-group">
                  <input type="text" class="form-control" id="barcode" name="barcode">
                  <button class="btn btn-outline-secondary" type="button" id="generate-barcode">Generate</button>
                </div>
              </div>
              <div class="col-md-4 d-flex align-items-end">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="track-quantity" name="track_quantity">
                  <label class="form-check-label" for="track-quantity">
                    Track Quantity
                  </label>
                </div>
              </div>
            </div>
            <div class="mt-3">
              <label for="locations" class="form-label">Locations</label>
              <input type="text" class="form-control" id="locations" name="locations" placeholder="e.g. Main warehouse, Branch 1">
              <!-- You can replace this with a multi-select if you have a list of locations -->
            </div>
          </div>
        </div>

        <!-- Variants -->
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="mb-3">Variants</h5>
            <div class="row g-3 align-items-end">
              <div class="col-md-5">
                <label for="option-name" class="form-label">Option Name</label>
                <input type="text" class="form-control" id="option-name" name="option_name[]" placeholder="e.g. Size">
              </div>
              <div class="col-md-7">
                <label for="option-values" class="form-label">Option Values</label>
                <input type="text" class="form-control" id="option-values" name="option_values[]" placeholder="e.g. Small, Medium, Large">
              </div>
            </div>
            <!-- You can use JS to add more options/variants as needed -->
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-lg-4">
        <!-- Status -->
        <div class="card mb-4">
          <div class="card-body">
            <h5>Status</h5>
            <div class="form-check mt-2">
              <input class="form-check-input" type="radio" name="status" id="published" value="published" checked>
              <label class="form-check-label" for="published">Published</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="status" id="draft" value="draft">
              <label class="form-check-label" for="draft">Draft</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="status" id="archived" value="archived">
              <label class="form-check-label" for="archived">Archived</label>
            </div>
          </div>
        </div>

        <!-- Product Organization -->
        <div class="card">
          <div class="card-body">
            <h5>Product Organization</h5>
            <div class="mb-3">
              <label for="product-type-org" class="form-label">Type</label>
              <input type="text" class="form-control" id="product-type-org" name="product_type_org">
            </div>
            <div class="mb-3">
              <label for="vendor" class="form-label">Vendor</label>
              <input type="text" class="form-control" id="vendor" name="vendor">
            </div>
            <div class="mb-3">
              <label for="categories" class="form-label">Categories</label>
              <input type="text" class="form-control" id="categories" name="categories" placeholder="e.g. Apparel, Electronics">
              <!-- Or use a multi-select if you have predefined categories -->
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Submit Button -->
    <div class="row">
      <div class="col-12 text-end">
        <button type="submit" class="btn btn-primary px-4">Save Product</button>
      </div>
    </div>
  </form>
</div>
