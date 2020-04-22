<template>
  <div>
    <div class="content">
      <div class="container">
        <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
        <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
          <p class="_title0">
            Categories
            <Button @click="addModal = true">
              <Icon type="md-add"></Icon>Add Category
            </Button>
          </p>

          <div class="_overflow _table_div">
            <table class="_table">
              <!-- TABLE TITLE -->
              <tr>
                <th>ID</th>
                <th>ICON IMAGE</th>
                <th>CATEGORY NAME</th>
                <th>CREATED AT</th>
                <th>Action</th>
              </tr>
              <!-- TABLE TITLE -->

              <!-- ITEMS -->
              <tr v-for="(category, i) in categories" :key="i" v-if="categories.length">
                <td>{{i+1}}</td>
                <td class="table_image">
                  <img class="img" :src="category.iconImage" />
                </td>
                <td class="_table_name">{{category.categoryName}}</td>
                <td>{{category.created_at}}</td>
                <td>
                  <Button type="info" shape="circle" @click="showEditModal(category, i)">Edit</Button>
                  <Button
                    type="error"
                    shape="circle"
                    @click="showDeletingModal(tag, i)"
                    :loading="category.isDeleting"
                  >Delete</Button>
                </td>
              </tr>
              <!-- ITEMS -->
            </table>
          </div>
        </div>

        <!-- Add Category modal -->
        <Modal v-model="addModal" title="Add Category" :mask-closable="false" :closable="false">
          <Input
            v-model="data.categoryName"
            placeholder="Enter category name..."
            style="width: 100%"
          />
          <div class="space"></div>
          <Upload
            ref="uploads"
            type="drag"
            action="app/upload"
            :headers="{'x-csrf-token':token, 'X-Requested-With': 'XMLHttpRequest'}"
            :on-success="handleSuccess"
            :max-size="2048"
            :format="['jpg','jpeg','png']"
            :on-format-error="handleFormatError"
            :on-exceeded-size="handleMaxSize"
            :before-upload="handleBeforeUpload"
            :on-error="handleError"
          >
            <div style="padding: 20px 0">
              <Icon type="ios-cloud-upload" size="52" style="color: #3399ff"></Icon>
              <p>Click or drag files here to upload</p>
            </div>
          </Upload>

          <template v-if="data.iconImage">
            <div class="demo-upload-list">
              <img :src="`/uploads/${data.iconImage}`" />
              <div class="demo-upload-list-cover">
                <Icon type="ios-trash-outline" @click="deleteImage"></Icon>
              </div>
            </div>
          </template>

          <div slot="footer">
            <Button type="default" @click="addModal=false">Close</Button>
            <Button
              type="primary"
              @click="addCategory"
              :disabled="isAdding"
              :loading="isAdding"
            >{{isAdding?'Adding...':'Add category'}}</Button>
          </div>
        </Modal>

        <!-- Edit tag modal -->
        <Modal v-model="editModal" title="Edit Tag" :mask-closable="false" :closable="false">
          <Input v-model="editData.tagName" placeholder="Edit tag name..." style="width: 100%" />

          <div slot="footer">
            <Button type="default" @click="editModal=false">Close</Button>
            <Button
              type="primary"
              @click="editTag"
              :disabled="isAdding"
              :loading="isAdding"
            >{{isAdding?'Editing...':'Edit tag'}}</Button>
          </div>
        </Modal>

        <!-- Delete confirmation modal -->
        <Modal v-model="showDeleteModal" width="360">
          <p slot="header" style="color:#f60;text-align:center">
            <Icon type="ios-information-circle"></Icon>
            <span>Delete confirmation</span>
          </p>
          <div style="text-align:center">
            <p>This action can not be reversed!</p>
            <p>Will you delete it?</p>
          </div>
          <div slot="footer">
            <Button
              type="error"
              size="large"
              long
              :loading="isDeleting"
              :disabled="isDeleting"
              @click="deleteTag"
            >Delete</Button>
          </div>
        </Modal>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      addModal: false,
      editModal: false,
      showDeleteModal: false,
      deleteItem: {},
      isDeleting: false,
      isAdding: false,
      categories: [],
      data: {
        categoryName: "",
        iconImage: ""
      },
      editData: {
        categoryName: "",
        iconImage: ""
      },
      index: -1,
      //
      token: ""
    };
  },

  methods: {
    async addCategory() {
      if (this.data.categoryName.trim() == "")
        return this.error("You can't keep the category name field empty");

      if (this.data.iconImage.trim() == "")
        return this.error("Icon Image is required");

      this.isAdding = true;
      const res = await this.callApi("post", "/app/create_category", this.data);
      if (res.status == 201) {
        this.categories.unshift(res.data); // push to first position
        this.addModal = false;
        this.data.categoryName = "";
        this.data.iconImage = "";
        this.$refs.uploads.clearFiles();
        this.success("Category added successfully");
      } else {
        if (res.status == 422) {
          if (res.data.errors.categoryName[0]) {
            this.error(res.data.errors.categoryName[0]);
          }
          if (res.data.errors.iconImage[0]) {
            this.error(res.data.errors.iconImage[0]);
          }
        } else {
          this.info("Something went wrong");
        }
      }
      this.isAdding = false;
    },

    async editTag() {
      if (this.editData.tagName.trim() == "")
        return this.error("You can't keep the tag name field empty");

      this.isAdding = true;
      const res = await this.callApi("post", "/app/edit_tag", this.editData);
      if (res.status == 200) {
        this.tags[this.index].tagName = this.editData.tagName;
        this.editModal = false;
        this.success("Tag edited successfully");
      } else {
        if (res.status == 422) {
          if (res.data.errors.tagName[0]) {
            this.error(res.data.errors.tagName[0]);
          }
        } else {
          this.info("Something went wrong");
        }
      }
      this.isAdding = false;
    },

    showEditModal(tag, index) {
      let obj = {
        id: tag.id,
        tagName: tag.tagName
      };
      this.editData = obj;
      this.editModal = true;
      this.index = index;
    },

    async deleteTag(tag, index) {
      // if (!confirm("Are you sure you want to delete?")) return;
      // this.$set(tag, "isDeleting", true); // set a new property that did not exsist to an object
      this.isDeleting = true;
      const res = await this.callApi("post", "app/delete_tag", this.deleteItem);
      if (res.status == 200) {
        this.tags.splice(this.index, 1); // remove one item from the array starting from the clicked tag
        this.success("Tag deleted successfully");
      } else {
        this.info("Something went wrong!");
      }
      this.isDeleting = false;
      this.showDeleteModal = false;
    },
    showDeletingModal(tag, index) {
      this.deleteItem = tag;
      this.index = index;
      this.showDeleteModal = true;
    },

    // Upload related methods
    async deleteImage() {
      let image = this.data.iconImage;
      this.data.iconImage = "";
      const res = await this.callApi("post", "app/delete_image", {
        imageName: image
      });
      if (res.status != 200) {
        this.data.iconImage = image;
        this.info("Something went wrong!");
      } else {
        this.$refs.uploads.clearFiles();
      }
    },
    handleSuccess(res, file) {
      this.data.iconImage = res;
    },
    handleError(res, file) {
      this.$Notice.warning({
        title: "The file format is incorrect",
        desc: `${
          file.errors.file.length
            ? file.errors.file[0]
            : "Something went wrong!"
        }`
      });
    },
    handleFormatError(file) {
      this.$Notice.warning({
        title: "The file format is incorrect",
        desc:
          "File format of " +
          file.name +
          " is incorrect, please select jpg, png or jpeg."
      });
    },
    handleMaxSize(file) {
      this.$Notice.warning({
        title: "Exceeding file size limit",
        desc: "File  " + file.name + " is too large, no more than 2M."
      });
    },
    handleBeforeUpload() {}
  },

  async created() {
    this.token = window.Laravel.csrfToken;
    const res = await this.callApi("get", "/app/get_categories");
    if (res.status == 200) {
      this.categories = res.data;
    } else {
      this.info("Something went wrong");
    }
  }
};
</script>

<style>
.demo-upload-list {
  display: inline-block;
  width: 60px;
  height: 60px;
  text-align: center;
  line-height: 60px;
  border: 1px solid transparent;
  border-radius: 4px;
  overflow: hidden;
  background: #fff;
  position: relative;
  box-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
  margin-right: 4px;
}
.demo-upload-list img {
  width: 100%;
  height: 100%;
}
.demo-upload-list-cover {
  display: none;
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgba(0, 0, 0, 0.6);
}
.demo-upload-list:hover .demo-upload-list-cover {
  display: block;
}
.demo-upload-list-cover i {
  color: #fff;
  font-size: 20px;
  cursor: pointer;
  margin: 0 2px;
}
</style>