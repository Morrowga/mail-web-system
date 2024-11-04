<script setup>
import { ref } from 'vue';
const incomingEmail = ref('<asai@xxx.com>');

</script>

<template>
    <VCardText>
        <VRow>
            <VCol cols="12" lg="2">
                <VIcon icon="mdi-trash-can-outline" />
            </VCol>
            <VCol cols="12" lg="6">
                <div class="icon-container">
                <div class="icon-wrapper">
                    <VIcon icon="mdi-arrow-left-top" class="icon-size" />
                    <div class="underline"></div>
                </div>
                <div class="icon-wrapper">
                    <VIcon icon="mdi-sync" class="icon-size" />
                    <div class="underline"></div>
                </div>
                <div class="icon-wrapper">
                    <VIcon icon="mdi-arrow-right-top" class="icon-size" />
                    <div class="underline"></div>
                </div>
                </div>
            </VCol>
            <VCol cols="12" lg="4">
                <div>
                    <p>Auguest 10,</p>
                    <p>10: 07</p>
                </div>
            </VCol>
        </VRow>
        <div class="my-4 mx-3">
            <p class="mail-subject">RE: [Men's] I have an appointment.</p>
            <div class="my-2">
                <p>Toshiyuki Okada</p>
                <p >
                    {{ incomingEmail }}
                </p>
                <div class="d-flex justify-between">
                    <p>
                        Attn:
                        <br>
                        MACHINO
                    </p>
                    <div>
                        <VBtn prepend-icon="mdi-triangle-down" style="background-color: transparent; border: 2px solid #000; box-shadow: none;">Completed</VBtn>
                    </div>
                </div>
            </div>
            <hr style="opacity: 0.3;">
            <div class="mt-3" style="margin-bottom: 3vh;">
                <div class="d-flex justify-between">
                    <div>
                        <p>
                            Attn:
                            <br>
                            MACHINO
                        </p>
                        <p class="ml-2">
                            Mr.Okada
                        </p>
                    </div>
                    <div>
                       <p>
                        Auguest 10,
                        <br />
                        10:07
                       </p>
                    </div>
                </div>
                <div class="ml-2 mt-5">
                    <p>Machino</p>
                    <p>Reservation Accpeted</p>
                </div>
            </div>
            <div class="my-2">
                <div class="d-flex justify-between">
                    <div>
                        <p>Toshiyuki Okada</p>
                        <p >
                            {{ incomingEmail }}
                        </p>
                    </div>
                    <div>
                       <p>
                        Auguest 10,
                        <br />
                        10:07
                       </p>
                    </div>
                </div>
                <div class="mt-3">
                    <p>
                        I would like to make an <br>
                        appointment for Auguest 20.
                        <br>
                        <br>
                        rice field in the charge of a nominal <br>
                        holder ( ritsuryo system )
                    </p>
                </div>
            </div>
        </div>
    </VCardText>
</template>

<style scoped>
.icon-container
{
  display: flex; /* Aligns icons in a row */
  justify-content: flex-start; /* Aligns items to the start without extra space */
  align-items: center; /* Centers icons vertically */
}

.icon-wrapper
{
  text-align: center; /* Centers icon and underline */
  margin: 0; /* Remove margins around the icon wrapper */
  padding-left: 8px;
  padding-right: 8px;
  padding-top: 5px;
}

.underline
{
  height: 2px; /* Thickness of the underline */
  background-color: rgb(0,0,0,0.3); /* Color of the underline */
  margin-top: 0; /* No space between icon and underline */
  width: 100%; /* Underline width */
}

.icon-size
{
    font-size: 27px;
    font-weight: medium;
    opacity: 0.4;
    color: #000;
}

.mail-subject{
    font-weight: bold;
    font-size: 15px;
}
</style>
