CC = gcc

TARGET = com_ssc_sa_json

MACHINE = $(shell $(CC) -dumpmachine)
# Windows (MinGW or Cygwin)
ifneq (,$(or $(findstring mingw, $(MACHINE)), $(findstring cygwin, $(MACHINE))))
	PLATFORM = WIN
	LIBS = -lm -lsetupapi
	#RM = del

# OS X (Mac)
else
ifneq (,$(findstring darwin, $(MACHINE)))
	PLATFORM = OSX

# POSIX (typically Linux)
else
	PLATFORM = POSIX
	LIBS = -lm
endif
endif

SRCS := $(wildcard *.c)
OBJS := $(SRCS:.c=.o)
DEPS := $(SRCS:.c=.d)

all: $(TARGET)

%.o: %.c
	$(CC) -O3 -Wall -c -fmessage-length=0 -DPLATFORM_$(PLATFORM) -MMD -MP -MF"$(@:%.o=%.d)" -MT"$(@:%.o=%.d)" -o"$@" "$<"

$(TARGET): $(OBJS)
	@echo 'Building target: $@'
	$(CC) -o"$(TARGET)" $(OBJS) $(LIBS)
	@echo 'Finished building target: $@'

clean:
	-$(RM) $(OBJS) $(DEPS) $(TARGET) $(TARGET).exe

.PHONY: all clean
