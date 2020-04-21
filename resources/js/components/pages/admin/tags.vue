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
                  <Button type="info" shape="circle">Edit</Button>
                  <Button type="error" shape="circle">Delete</Button>
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
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      addModal: false,
      isAdding: false,
      tags: [],
      data: {
        tagName: ""
      }
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
        this.isAdding = false;
      }
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