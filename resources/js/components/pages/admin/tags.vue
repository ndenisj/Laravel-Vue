<template>
  <div>
    <div class="content">
      <div class="container">
        <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
        <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
          <p class="_title0">
            TAGS
            <Button @click="addModal = true">
              <Icon type="md-add"></Icon>Add Tag
            </Button>
          </p>

          <div class="_overflow _table_div">
            <table class="_table">
              <!-- TABLE TITLE -->
              <tr>
                <th>ID</th>
                <th>TAG NAME</th>
                <th>CREATED AT</th>
                <th>Action</th>
              </tr>
              <!-- TABLE TITLE -->

              <!-- ITEMS -->
              <tr v-for="(tag, i) in tags" :key="i" v-if="tags.length">
                <td>{{i+1}}</td>
                <td class="_table_name">{{tag.tagName}}</td>
                <td>{{tag.created_at}}</td>
                <td>
                  <Button type="info" shape="circle" @click="showEditModal(tag, i)">Edit</Button>
                  <Button
                    type="error"
                    shape="circle"
                    @click="showDeletingModal(tag, i)"
                    :loading="tag.isDeleting"
                  >Delete</Button>
                </td>
              </tr>
              <!-- ITEMS -->
            </table>
          </div>
        </div>

        <!-- Add tag modal -->
        <Modal v-model="addModal" title="Add Tag" :mask-closable="false" :closable="false">
          <Input v-model="data.tagName" placeholder="Enter tag name..." style="width: 100%" />

          <div slot="footer">
            <Button type="default" @click="addModal=false">Close</Button>
            <Button
              type="primary"
              @click="addTag"
              :disabled="isAdding"
              :loading="isAdding"
            >{{isAdding?'Adding...':'Add tag'}}</Button>
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
      tags: [],
      data: {
        tagName: ""
      },
      editData: {
        tagName: ""
      },
      index: -1
    };
  },

  methods: {
    async addTag() {
      if (this.data.tagName.trim() == "")
        return this.error("You can't keep the tag name field empty");

      this.isAdding = true;
      const res = await this.callApi("post", "/app/create_tag", this.data);
      if (res.status == 201) {
        this.tags.unshift(res.data); // push to first position
        this.addModal = false;
        this.data.tagName = "";
        this.success("Tag added successfully");
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
    }
  },

  async created() {
    const res = await this.callApi("get", "/app/get_tags");
    if (res.status == 200) {
      this.tags = res.data;
    } else {
      this.info("Something went wrong");
    }
  }
};
</script>